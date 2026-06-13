const pool = require("../config/pool");

class RequestService {
    // ==================== Cancellation Requests ====================

    /**
     * Create a cancellation request for a confirmed booking.
     * @param {object} connOrPool - Transaction connection or pool
     * @param {number} bookingId
     * @param {number} userId
     * @param {string|null} reason
     * @returns {Promise<number>} - The request_id
     */
    static async createCancellationRequest(
        connOrPool,
        bookingId,
        userId,
        reason = null,
    ) {
        const [result] = await connOrPool.query(
            "INSERT INTO cancellation_requests (booking_id, user_id, reason, status) VALUES (?, ?, ?, ?)",
            [bookingId, userId, reason, "pending"],
        );
        return result.insertId;
    }

    /**
     * Get cancellation request by ID.
     * @param {object} connOrPool
     * @param {number} requestId
     * @returns {Promise<object|null>}
     */
    static async getCancellationRequestById(connOrPool, requestId) {
        const [rows] = await connOrPool.query(
            `SELECT cr.*, b.booking_code, b.status as booking_status, b.schedule_id, b.passenger_name,
                    r.origin_city, r.destination_city, s.departure_time
             FROM cancellation_requests cr
             JOIN bookings b ON cr.booking_id = b.booking_id
             JOIN schedules s ON b.schedule_id = s.schedule_id
             JOIN routes r ON s.route_id = r.route_id
             WHERE cr.request_id = ?`,
            [requestId],
        );
        return rows.length > 0 ? rows[0] : null;
    }

    /**
     * Get all cancellation requests.
     * @param {object} connOrPool
     * @returns {Promise<object[]>}
     */
    static async getAllCancellationRequests(connOrPool) {
        const [rows] = await connOrPool.query(
            `SELECT cr.request_id, cr.booking_id, cr.user_id, cr.reason, cr.status, cr.requested_at, cr.reviewed_at,
                    u.email as customer_email, b.booking_code, b.passenger_name,
                    r.origin_city, r.destination_city, s.departure_time
             FROM cancellation_requests cr
             JOIN users u ON cr.user_id = u.id
             JOIN bookings b ON cr.booking_id = b.booking_id
             JOIN schedules s ON b.schedule_id = s.schedule_id
             JOIN routes r ON s.route_id = r.route_id
             ORDER BY cr.requested_at DESC`,
        );
        return rows;
    }

    /**
     * Get cancellation requests for a specific booking.
     * @param {object} connOrPool
     * @param {number} bookingId
     * @returns {Promise<object[]>}
     */
    static async getCancellationRequestsByBookingId(connOrPool, bookingId) {
        const [rows] = await connOrPool.query(
            "SELECT * FROM cancellation_requests WHERE booking_id = ? ORDER BY requested_at DESC",
            [bookingId],
        );
        return rows;
    }

    /**
     * Approve cancellation request - cancels the booking within transaction.
     * @param {object} connOrPool - Must be transaction connection
     * @param {number} requestId
     * @param {number} adminId
     * @param {string|null} adminNotes
     * @returns {Promise<boolean>}
     */
    static async approveCancellationRequest(
        connOrPool,
        requestId,
        adminId,
        adminNotes = null,
    ) {
        const request = await this.getCancellationRequestById(
            connOrPool,
            requestId,
        );
        if (!request) return false;

        await connOrPool.query(
            "UPDATE cancellation_requests SET status = ?, reviewed_at = NOW(), reviewed_by = ?, admin_notes = ? WHERE request_id = ?",
            ["approved", adminId, adminNotes, requestId],
        );

        // Cancel the booking (seats released by status change)
        await connOrPool.query(
            "UPDATE bookings SET status = ? WHERE booking_id = ?",
            ["cancelled", request.booking_id],
        );

        return true;
    }

    /**
     * Reject cancellation request.
     * @param {object} connOrPool
     * @param {number} requestId
     * @param {number} adminId
     * @param {string|null} adminNotes
     * @returns {Promise<boolean>}
     */
    static async rejectCancellationRequest(
        connOrPool,
        requestId,
        adminId,
        adminNotes = null,
    ) {
        const [result] = await connOrPool.query(
            "UPDATE cancellation_requests SET status = ?, reviewed_at = NOW(), reviewed_by = ?, admin_notes = ? WHERE request_id = ?",
            ["rejected", adminId, adminNotes, requestId],
        );
        return result.affectedRows > 0;
    }

    // ==================== Reschedule Requests ====================

    /**
     * Create a reschedule request for a confirmed booking.
     * @param {object} connOrPool
     * @param {number} bookingId
     * @param {number} userId
     * @param {number} targetScheduleId
     * @param {string} targetSeatNumber
     * @param {string|null} reason
     * @returns {Promise<number>}
     */
    static async createRescheduleRequest(
        connOrPool,
        bookingId,
        userId,
        targetScheduleId,
        targetSeatNumber,
        reason = null,
    ) {
        const [result] = await connOrPool.query(
            "INSERT INTO reschedule_requests (booking_id, user_id, target_schedule_id, target_seat_number, reason, status) VALUES (?, ?, ?, ?, ?, ?)",
            [
                bookingId,
                userId,
                targetScheduleId,
                targetSeatNumber,
                reason,
                "pending",
            ],
        );
        return result.insertId;
    }

    /**
     * Get reschedule request by ID with full details.
     * @param {object} connOrPool
     * @param {number} requestId
     * @returns {Promise<object|null>}
     */
    static async getRescheduleRequestById(connOrPool, requestId) {
        const [rows] = await connOrPool.query(
            `SELECT rr.*, b.booking_code, b.schedule_id as current_schedule_id, b.passenger_name,
                    r.origin_city, r.destination_city, s.departure_time as current_departure,
                    ts.schedule_id as target_schedule_id, tr.origin_city as target_origin, tr.destination_city as target_destination, ts.departure_time as target_departure
             FROM reschedule_requests rr
             JOIN bookings b ON rr.booking_id = b.booking_id
             JOIN schedules s ON b.schedule_id = s.schedule_id
             JOIN routes r ON s.route_id = r.route_id
             JOIN schedules ts ON rr.target_schedule_id = ts.schedule_id
             JOIN routes tr ON ts.route_id = tr.route_id
             WHERE rr.request_id = ?`,
            [requestId],
        );
        return rows.length > 0 ? rows[0] : null;
    }

    /**
     * Get all reschedule requests.
     * @param {object} connOrPool
     * @returns {Promise<object[]>}
     */
    static async getAllRescheduleRequests(connOrPool) {
        const [rows] = await connOrPool.query(
            `SELECT rr.request_id, rr.booking_id, rr.user_id, rr.target_schedule_id, rr.target_seat_number, rr.reason, rr.status, rr.requested_at,
                    u.email as customer_email, b.booking_code, b.passenger_name,
                    s.departure_time as target_departure
             FROM reschedule_requests rr
             JOIN users u ON rr.user_id = u.id
             JOIN bookings b ON rr.booking_id = b.booking_id
             JOIN schedules s ON rr.target_schedule_id = s.schedule_id
             ORDER BY rr.requested_at DESC`,
        );
        return rows;
    }

    /**
     * Get reschedule requests for a specific booking.
     * @param {object} connOrPool
     * @param {number} bookingId
     * @returns {Promise<object[]>}
     */
    static async getRescheduleRequestsByBookingId(connOrPool, bookingId) {
        const [rows] = await connOrPool.query(
            "SELECT * FROM reschedule_requests WHERE booking_id = ? ORDER BY requested_at DESC",
            [bookingId],
        );
        return rows;
    }

    /**
     * Approve reschedule request - updates booking within transaction.
     * Uses seatAvailability.assertSeatAvailable for conflict checking.
     * @param {object} connOrPool - Transaction connection
     * @param {number} requestId
     * @param {number} adminId
     * @param {string|null} adminNotes
     * @returns {Promise<{success: boolean, error?: string}>}
     */
    static async approveRescheduleRequest(
        connOrPool,
        requestId,
        adminId,
        adminNotes = null,
    ) {
        const seatAvailability = require("./seatAvailability");

        const request = await this.getRescheduleRequestById(
            connOrPool,
            requestId,
        );
        if (!request) {
            return { success: false, error: "Request not found" };
        }

        // Get vehicle for the target schedule
        const [schedules] = await connOrPool.query(
            "SELECT vehicle_id, price FROM schedules WHERE schedule_id = ?",
            [request.target_schedule_id],
        );
        if (schedules.length === 0) {
            return { success: false, error: "Target schedule not found" };
        }
        const targetSchedule = schedules[0];

        // Find or create the target seat
        let seatId = null;
        const [seats] = await connOrPool.query(
            "SELECT seat_id FROM seats WHERE vehicle_id = ? AND seat_number = ?",
            [targetSchedule.vehicle_id, request.target_seat_number],
        );
        if (seats.length > 0) {
            seatId = seats[0].seat_id;
        } else {
            const [sRes] = await connOrPool.query(
                "INSERT INTO seats (vehicle_id, seat_number) VALUES (?, ?)",
                [targetSchedule.vehicle_id, request.target_seat_number],
            );
            seatId = sRes.insertId;
        }

        // Check seat availability
        try {
            await seatAvailability.assertSeatAvailable(
                connOrPool,
                request.target_schedule_id,
                seatId,
                request.booking_id,
            );
        } catch (err) {
            if (err.message === "SEAT_TAKEN") {
                return {
                    success: false,
                    error: "Target seat is already taken",
                };
            }
            throw err;
        }

        // Update the request
        await connOrPool.query(
            "UPDATE reschedule_requests SET status = ?, reviewed_at = NOW(), reviewed_by = ?, admin_notes = ? WHERE request_id = ?",
            ["approved", adminId, adminNotes, requestId],
        );

        // Update booking with new schedule and seat
        await connOrPool.query(
            "UPDATE bookings SET schedule_id = ?, total_amount = ? WHERE booking_id = ?",
            [
                request.target_schedule_id,
                targetSchedule.price,
                request.booking_id,
            ],
        );

        // Update booking_seats
        await connOrPool.query(
            "DELETE FROM booking_seats WHERE booking_id = ?",
            [request.booking_id],
        );
        await connOrPool.query(
            "INSERT INTO booking_seats (booking_id, seat_id, price_at_booking) VALUES (?, ?, ?)",
            [request.booking_id, seatId, targetSchedule.price],
        );

        return { success: true };
    }

    /**
     * Reject reschedule request.
     * @param {object} connOrPool
     * @param {number} requestId
     * @param {number} adminId
     * @param {string|null} adminNotes
     * @returns {Promise<boolean>}
     */
    static async rejectRescheduleRequest(
        connOrPool,
        requestId,
        adminId,
        adminNotes = null,
    ) {
        const [result] = await connOrPool.query(
            "UPDATE reschedule_requests SET status = ?, reviewed_at = NOW(), reviewed_by = ?, admin_notes = ? WHERE request_id = ?",
            ["rejected", adminId, adminNotes, requestId],
        );
        return result.affectedRows > 0;
    }
}

module.exports = RequestService;
