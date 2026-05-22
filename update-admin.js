const mysql = require('mysql2/promise');
(async () => {
  try {
    const conn = await mysql.createConnection({host: 'localhost', user: 'root', database: 'travelgo'});
    const username = 'admin';
    const passwordHash = '$2b$12$GdB3OOLuchE7aZSzI7DAMeRxZzrIt0d8BZbebkLrFeT86rVCtnKZS'; // hash of 'admin123'
    await conn.execute('UPDATE users SET username = ?, password = ? WHERE id = 1', [username, passwordHash]);
    console.log('Admin credentials updated');
    await conn.end();
  } catch (err) {
    console.error('Error:', err);
  }
})();
