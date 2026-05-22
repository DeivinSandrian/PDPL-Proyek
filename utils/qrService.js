const QRCode = require('qrcode');

module.exports = {
    generateBase64: async (text) => {
        try {
            return await QRCode.toDataURL(text, { margin: 1, width: 300 });
        } catch (err) {
            console.error('QR generation error:', err);
            return null;
        }
    }
};
