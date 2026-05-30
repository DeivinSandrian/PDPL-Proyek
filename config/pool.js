const mysql = require('mysql2/promise');
const dbConfig = require('./database');

const pool = mysql.createPool({
    ...dbConfig,
    waitForConnections: true,
    connectionLimit: 10
});

module.exports = pool;
