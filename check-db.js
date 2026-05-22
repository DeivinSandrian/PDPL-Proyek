const mysql = require('mysql2/promise');

(async () => {
    try {
        const conn = await mysql.createConnection({ host: 'localhost', user: 'root', database: 'travelgo' });
        
        console.log('--- VEHICLES ---');
        const [vehicles] = await conn.query('DESCRIBE vehicles');
        console.table(vehicles);
        
        console.log('--- SCHEDULES ---');
        const [schedules] = await conn.query('DESCRIBE schedules');
        console.table(schedules);
        
        console.log('--- BOOKINGS ---');
        const [bookings] = await conn.query('DESCRIBE bookings');
        console.table(bookings);

        await conn.end();
    } catch(err) {
        console.error('Error:', err);
    }
})();
