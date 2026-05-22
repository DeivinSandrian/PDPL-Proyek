const mysql = require('mysql2/promise');

(async () => {
    try {
        const conn = await mysql.createConnection({ host: 'localhost', user: 'root', database: 'travelgo' });
        console.log('Connected to database.');

        try {
            await conn.query('ALTER TABLE schedules ADD COLUMN pickup_point VARCHAR(255) DEFAULT NULL, ADD COLUMN dropoff_point VARCHAR(255) DEFAULT NULL');
            console.log('Added pickup_point and dropoff_point to schedules.');
        } catch(e) {
            console.log('Columns pickup_point or dropoff_point might already exist.', e.message);
        }

        try {
            await conn.query('ALTER TABLE bookings ADD COLUMN passenger_name VARCHAR(255) DEFAULT NULL');
            console.log('Added passenger_name to bookings.');
        } catch(e) {
            console.log('Column passenger_name might already exist.', e.message);
        }

        await conn.end();
        console.log('Database update complete.');
    } catch(err) {
        console.error('Error updating database:', err);
    }
})();
