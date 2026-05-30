const fs = require('fs');
const path = require('path');

function parseEnvLine(line) {
    const trimmed = line.trim();
    if (!trimmed || trimmed.startsWith('#')) return null;

    const equalsIndex = trimmed.indexOf('=');
    if (equalsIndex === -1) return null;

    const key = trimmed.slice(0, equalsIndex).trim();
    let value = trimmed.slice(equalsIndex + 1).trim();

    if ((value.startsWith('"') && value.endsWith('"')) || (value.startsWith("'") && value.endsWith("'"))) {
        value = value.slice(1, -1);
    }

    return { key, value };
}

function loadEnv(filePath = path.resolve(__dirname, '..', '.env')) {
    if (!fs.existsSync(filePath)) return;

    const content = fs.readFileSync(filePath, 'utf8');
    for (const line of content.split(/\r?\n/)) {
        const parsed = parseEnvLine(line);
        if (!parsed || process.env[parsed.key] !== undefined) continue;
        process.env[parsed.key] = parsed.value;
    }
}

module.exports = { loadEnv };
