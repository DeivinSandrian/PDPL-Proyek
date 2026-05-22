const express = require('express');
const router = express.Router();
const authAdmin = require('../middleware/authAdmin');
const mysql = require('mysql2/promise');
const qrService = require('../utils/qrService');

const pool = mysql.createPool({
    host: 'localhost',
    user: 'root',
    database: 'travelgo',
    waitForConnections: true,
    connectionLimit: 10
});

/* Dashboard */
router.get('/dashboard', authAdmin, async (req, res) => {
    try {
        const [[bookingCount]] = await pool.query('SELECT COUNT(*) AS total FROM bookings');
        const [[confirmedCount]] = await pool.query("SELECT COUNT(*) AS total FROM bookings WHERE status='confirmed'");
        const [[revenueResult]] = await pool.query("SELECT IFNULL(SUM(total_amount),0) AS total FROM bookings WHERE status='confirmed'");
        const [[scheduleCount]] = await pool.query('SELECT COUNT(*) AS total FROM schedules');
        const [[vehicleCount]] = await pool.query('SELECT COUNT(*) AS total FROM vehicles');
        res.render('admin/dashboard', {
            title: 'Dashboard Admin',
            user: req.session.user,
            stats: {
                totalBookings: bookingCount.total,
                confirmed: confirmedCount.total,
                revenue: revenueResult.total,
                schedules: scheduleCount.total,
                vehicles: vehicleCount.total
            }
        });
    } catch (err) {
        console.error(err);
        res.render('admin/dashboard', {
            title: 'Dashboard Admin',
            user: req.session.user,
            stats: { totalBookings: 0, confirmed: 0, revenue: 0, schedules: 0, vehicles: 0 }
        });
    }
});

/* Schedules */
router.get('/schedules', authAdmin, async (req, res) => {
    const [schedules] = await pool.query(`
        SELECT s.*, v.plate_number AS vehicle_name, r.origin_city AS origin, r.destination_city AS destination 
        FROM schedules s 
        LEFT JOIN vehicles v ON s.vehicle_id = v.vehicle_id 
        LEFT JOIN routes r ON s.route_id = r.route_id 
        ORDER BY s.departure_time DESC
    `);
    const [vehicles] = await pool.query('SELECT * FROM vehicles');
    res.render('admin/schedules', { title: 'Manajemen Jadwal', schedules, vehicles, user: req.session.user });
});
router.post('/schedules', authAdmin, async (req, res) => {
    const { origin, destination, departure, arrival, vehicle_id, price, pickup_point, dropoff_point } = req.body;
    // Find or create route
    let route_id = null;
    const [routes] = await pool.query('SELECT route_id FROM routes WHERE origin_city=? AND destination_city=?', [origin, destination]);
    if (routes.length > 0) {
        route_id = routes[0].route_id;
    } else {
        const [result] = await pool.query('INSERT INTO routes (origin_city, destination_city) VALUES (?, ?)', [origin, destination]);
        route_id = result.insertId;
    }

    await pool.query(
        'INSERT INTO schedules (route_id,vehicle_id,departure_time,arrival_estimate,price,pickup_point,dropoff_point,status) VALUES (?,?,?,?,?,?,?,?)', 
        [route_id, vehicle_id || null, departure, arrival, price, pickup_point || null, dropoff_point || null, 'available']
    );
    res.redirect('/admin/schedules');
});
router.post('/schedules/delete/:id', authAdmin, async (req, res) => {
    await pool.query('DELETE FROM schedules WHERE schedule_id=?', [req.params.id]);
    res.redirect('/admin/schedules');
});

/* Vehicles */
router.get('/vehicles', authAdmin, async (req, res) => {
    const [vehicles] = await pool.query('SELECT * FROM vehicles ORDER BY vehicle_id DESC');
    res.render('admin/vehicles', { title: 'Manajemen Kendaraan', vehicles, user: req.session.user });
});
router.post('/vehicles', authAdmin, async (req, res) => {
    const { name, type, capacity, image_url } = req.body;
    await pool.query('INSERT INTO vehicles (plate_number,vehicle_type,capacity,status) VALUES (?,?,?,?)', [name, type, capacity, 'active']);
    res.redirect('/admin/vehicles');
});
router.post('/vehicles/delete/:id', authAdmin, async (req, res) => {
    await pool.query('DELETE FROM vehicles WHERE vehicle_id=?', [req.params.id]);
    res.redirect('/admin/vehicles');
});

/* Bookings */
router.get('/bookings', authAdmin, async (req, res) => {
    const [bookings] = await pool.query(`
        SELECT b.booking_id as id, u.email, r.origin_city AS origin, r.destination_city AS destination, b.status, b.created_at, b.passenger_name,
        (SELECT GROUP_CONCAT(seat_id) FROM booking_seats WHERE booking_id = b.booking_id) as seat_number
        FROM bookings b
        LEFT JOIN users u ON b.user_id = u.id
        JOIN schedules s ON b.schedule_id = s.schedule_id
        JOIN routes r ON s.route_id = r.route_id
        ORDER BY b.created_at DESC
    `);
    res.render('admin/bookings', { title: 'Manajemen Pemesanan', bookings, user: req.session.user });
});
router.post('/bookings/status/:id', authAdmin, async (req, res) => {
    const { status } = req.body;
    // Map status if needed, but enum is pending, confirmed, cancelled, expired
    let dbStatus = status;
    if (status === 'canceled') dbStatus = 'cancelled';
    if (status === 'refunded') dbStatus = 'cancelled'; // simplificiation
    await pool.query('UPDATE bookings SET status=? WHERE booking_id=?', [dbStatus, req.params.id]);
    const io = req.app.get('io');
    if (io) io.emit('bookingUpdate', { id: req.params.id, status: dbStatus });
    res.redirect('/admin/bookings');
});

/* Offline Booking */
router.get('/bookings/new', authAdmin, async (req, res) => {
    const [schedules] = await pool.query(`
        SELECT s.*, r.origin_city AS origin, r.destination_city AS destination 
        FROM schedules s JOIN routes r ON s.route_id = r.route_id 
        WHERE s.departure_time > NOW() ORDER BY s.departure_time ASC
    `);
    res.render('admin/booking-new', { title: 'Tambah Pemesanan Offline', schedules, user: req.session.user });
});

router.post('/bookings/new', authAdmin, async (req, res) => {
    const { schedule_id, passenger_name, seat_number } = req.body;
    const user_id = req.session.user.id; 
    const booking_code = Math.random().toString(36).substring(2, 10).toUpperCase();

    // Get price
    const [[schedule]] = await pool.query('SELECT price FROM schedules WHERE schedule_id=?', [schedule_id]);

    const [result] = await pool.query(
        "INSERT INTO bookings (user_id, schedule_id, booking_code, booking_channel, total_amount, passenger_name, status, created_at) VALUES (?, ?, ?, 'offline', ?, ?, 'confirmed', NOW())",
        [user_id, schedule_id, booking_code, schedule.price, passenger_name]
    );
    
    // Insert seat
    await pool.query('INSERT INTO booking_seats (booking_id, seat_id) VALUES (?, ?)', [result.insertId, seat_number]);

    res.redirect('/admin/bookings');
});

/* Reschedule */
router.get('/bookings/reschedule/:id', authAdmin, async (req, res) => {
    const [bookings] = await pool.query(`
        SELECT b.booking_id as id, b.*, r.origin_city AS origin, r.destination_city AS destination 
        FROM bookings b JOIN schedules s ON b.schedule_id = s.schedule_id JOIN routes r ON s.route_id = r.route_id 
        WHERE b.booking_id = ?
    `, [req.params.id]);
    if (!bookings.length) return res.redirect('/admin/bookings');
    
    const booking = bookings[0];
    const [schedules] = await pool.query(`
        SELECT s.*, r.origin_city AS origin, r.destination_city AS destination 
        FROM schedules s JOIN routes r ON s.route_id = r.route_id 
        WHERE r.origin_city = ? AND r.destination_city = ? AND s.departure_time > NOW() 
        ORDER BY s.departure_time ASC
    `, [booking.origin, booking.destination]);
    
    res.render('admin/booking-reschedule', { title: 'Reschedule Pesanan', booking, schedules, user: req.session.user });
});

router.post('/bookings/reschedule/:id', authAdmin, async (req, res) => {
    const { schedule_id, seat_number } = req.body;
    await pool.query('UPDATE bookings SET schedule_id=? WHERE booking_id=?', [schedule_id, req.params.id]);
    await pool.query('DELETE FROM booking_seats WHERE booking_id=?', [req.params.id]);
    await pool.query('INSERT INTO booking_seats (booking_id, seat_id) VALUES (?, ?)', [req.params.id, seat_number]);
    res.redirect('/admin/bookings');
});

/* Notifications */
router.get('/notifications', authAdmin, async (req, res) => {
    // Assuming notifications table has an admin_id? The user schema shows no admin_id. 
    // Let's just create the table if missing or skip it.
    try {
        const [notifications] = await pool.query('SELECT * FROM notifications ORDER BY created_at DESC');
        res.render('admin/notifications', { title: 'Notifikasi', notifications, user: req.session.user });
    } catch(e) {
        res.render('admin/notifications', { title: 'Notifikasi', notifications: [], user: req.session.user });
    }
});
router.post('/notifications', authAdmin, async (req, res) => {
    try {
        const { message } = req.body;
        // The table notifications might require data column for laravel?
        // Skip DB insert for now, just emit
        const io = req.app.get('io');
        if (io) io.emit('adminNotification', { message });
    } catch(e) {}
    res.redirect('/admin/notifications');
});

/* E-Ticket QR */
router.get('/eticket/:bookingId', authAdmin, async (req, res) => {
    const [rows] = await pool.query(`
        SELECT b.booking_id as id, b.*, r.origin_city AS origin, r.destination_city AS destination, s.departure_time as departure, u.email,
        (SELECT GROUP_CONCAT(seat_id) FROM booking_seats WHERE booking_id = b.booking_id) as seat_number
        FROM bookings b
        JOIN schedules s ON b.schedule_id = s.schedule_id
        JOIN routes r ON s.route_id = r.route_id
        LEFT JOIN users u ON b.user_id = u.id
        WHERE b.booking_id=?
    `, [req.params.bookingId]);
    if (!rows.length) return res.status(404).send('Booking not found');
    const booking = rows[0];
    const qrData = `TRAVELGO|BookingID:${booking.id}|Route:${booking.origin}-${booking.destination}|Seat:${booking.seat_number}|Email:${booking.email}`;
    const qrImg = await qrService.generateBase64(qrData);
    res.render('admin/eticket', { title: 'E-Ticket', qrImg, booking, user: req.session.user });
});

module.exports = router;
