/**
 * Validation utility functions for TravelGo Express
 */
class Validation {
    /**
     * Checks if all required fields are present and not empty in target object.
     * @param {string[]} requiredFields 
     * @param {object} obj 
     * @returns {string|null} Error message or null if valid
     */
    static validateRequired(requiredFields, obj) {
        if (!obj) return 'Data input is missing.';
        for (const field of requiredFields) {
            if (obj[field] === undefined || obj[field] === null || String(obj[field]).trim() === '') {
                return `Field '${field}' is required.`;
            }
        }
        return null;
    }

    /**
     * Checks if price is a valid positive number.
     * @param {any} price 
     * @returns {boolean}
     */
    static isValidPrice(price) {
        if (price === null || price === undefined || price === '') return false;
        const num = Number(price);
        return !isNaN(num) && num >= 0;
    }

    /**
     * Checks if date string is a valid ISO or local date.
     * @param {string} dateStr 
     * @returns {boolean}
     */
    static isValidDate(dateStr) {
        if (dateStr === null || dateStr === undefined || String(dateStr).trim() === '') return false;
        const d = new Date(dateStr);
        return d instanceof Date && !isNaN(d.getTime());
    }

    /**
     * Checks if value is in an array of allowed values.
     * @param {any} val 
     * @param {any[]} allowed 
     * @returns {boolean}
     */
    static isValidEnum(val, allowed) {
        return allowed.includes(val);
    }
}

module.exports = Validation;
