const mysql = require('mysql2/promise');
const dbConfig = require('./config/database');

(async () => {
    try {
        const conn = await mysql.createConnection(dbConfig);

        const [tables] = await conn.query(`
            SELECT TABLE_NAME
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = ?
            ORDER BY TABLE_NAME
        `, [dbConfig.database]);

        console.log(`Connected to ${dbConfig.database}.`);
        console.log('Tables:');
        for (const table of tables) {
            console.log(`- ${table.TABLE_NAME}`);
        }

        await conn.end();
    } catch (err) {
        console.error('Error:', err.message);
        process.exit(1);
    }
})();
