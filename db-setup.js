const mysql = require('mysql2/promise');
const bcrypt = require('bcrypt');

(async () => {
    try {
        const conn = await mysql.createConnection({ host: 'localhost', user: 'root', database: 'travelgo' });
        console.log('Connected to travelgo database.');

        // Create vehicles table
        await conn.query(`
            CREATE TABLE IF NOT EXISTS vehicles (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                type VARCHAR(100) NOT NULL,
                capacity INT NOT NULL,
                image_url VARCHAR(500),
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            ) ENGINE=InnoDB
        `);
        console.log('Table vehicles created.');

        // Create schedules table
        await conn.query(`
            CREATE TABLE IF NOT EXISTS schedules (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                origin VARCHAR(255) NOT NULL,
                destination VARCHAR(255) NOT NULL,
                departure DATETIME NOT NULL,
                arrival DATETIME NOT NULL,
                vehicle_id BIGINT UNSIGNED,
                price DECIMAL(10,2) NOT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL
            ) ENGINE=InnoDB
        `);
        console.log('Table schedules created.');

        // Create bookings table
        await conn.query(`
            CREATE TABLE IF NOT EXISTS bookings (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                schedule_id BIGINT UNSIGNED NOT NULL,
                seat_number VARCHAR(10) NOT NULL,
                status ENUM('pending','confirmed','canceled','refunded') DEFAULT 'pending',
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE CASCADE
            ) ENGINE=InnoDB
        `);
        console.log('Table bookings created.');

        // Create notifications table
        await conn.query(`
            CREATE TABLE IF NOT EXISTS notifications (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                admin_id BIGINT UNSIGNED NOT NULL,
                message TEXT NOT NULL,
                read_at TIMESTAMP NULL,
                created_at TIMESTAMP NULL,
                FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB
        `);
        console.log('Table notifications created.');

        // Ensure username column exists on users table
        try {
            await conn.query('ALTER TABLE users ADD COLUMN username VARCHAR(255) UNIQUE AFTER email');
            console.log('Added username column to users.');
        } catch (e) {
            // column already exists, ignore
        }

        // Update admin credentials
        const adminHash = await bcrypt.hash('admin123', 12);
        await conn.query('UPDATE users SET username = ?, password = ? WHERE email = ?', ['admin', adminHash, 'admin@travelgo.com']);
        console.log('Admin credentials updated: email=admin@travelgo.com, username=admin, password=admin123');

        // Update customer credentials
        const custHash = await bcrypt.hash('cust123', 12);
        await conn.query('UPDATE users SET username = ?, password = ? WHERE email = ?', ['budi', custHash, 'budi@example.com']);
        console.log('Customer credentials updated: email=budi@example.com, username=budi, password=cust123');

        await conn.query('UPDATE users SET username = ?, password = ? WHERE email = ?', ['siti', custHash, 'siti@example.com']);
        console.log('Customer credentials updated: email=siti@example.com, username=siti, password=cust123');

        console.log('\n=== SETUP COMPLETE ===');
        console.log('Admin login: email=admin@travelgo.com | username=admin | password=admin123');
        console.log('Customer login: email=budi@example.com | username=budi | password=cust123');

        await conn.end();
        process.exit(0);
    } catch (err) {
        console.error('Setup error:', err);
        process.exit(1);
    }
})();
