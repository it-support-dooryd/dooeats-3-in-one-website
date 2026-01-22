/**
 * Order Status Display Mapping Helper
 * Maps internal database status values to user-friendly display labels
 */

/**
 * Get customer-facing status label
 * @param {string} dbStatus - The database status value
 * @returns {string} - Customer-friendly status label
 */
function getCustomerOrderStatus(dbStatus) {
    const statusMap = {
        'Order Placed': 'Order Placed',
        'Order Accepted': 'Restaurant Confirming',
        'Driver Pending': 'Preparing Food',
        'Driver Accepted': 'Preparing Food',
        'Order Shipped': 'Out for Delivery',
        'In Transit': 'Out for Delivery',
        'Order Completed': 'Delivered',
        'Order Rejected': 'Order Cancelled',
        'Driver Rejected': 'Order Cancelled'
    };

    return statusMap[dbStatus] || dbStatus;
}

/**
 * Get restaurant-facing status label
 * @param {string} dbStatus - The database status value
 * @returns {string} - Restaurant-friendly status label
 */
function getRestaurantOrderStatus(dbStatus) {
    const statusMap = {
        'Order Placed': 'New Order',
        'Order Accepted': 'Cooking',
        'Driver Pending': 'Food Ready',
        'Driver Accepted': 'Handover',
        'Order Shipped': 'Handover',
        'In Transit': 'Out for Delivery',
        'Order Completed': 'Completed',
        'Order Rejected': 'Rejected',
        'Driver Rejected': 'Rejected'
    };

    return statusMap[dbStatus] || dbStatus;
}

/**
 * Get status progress percentage for customer
 * @param {string} dbStatus - The database status value
 * @returns {number} - Progress percentage (0-100)
 */
function getOrderProgress(dbStatus) {
    const progressMap = {
        'Order Placed': 16,
        'Order Accepted': 33,
        'Driver Pending': 50,
        'Driver Accepted': 50,
        'Order Shipped': 66,
        'In Transit': 83,
        'Order Completed': 100,
        'Order Rejected': 0,
        'Driver Rejected': 0
    };

    return progressMap[dbStatus] || 0;
}

/**
 * Get status color/badge class
 * @param {string} dbStatus - The database status value
 * @returns {string} - CSS class for status badge
 */
function getOrderStatusClass(dbStatus) {
    const classMap = {
        'Order Placed': 'badge-info',
        'Order Accepted': 'badge-primary',
        'Driver Pending': 'badge-warning',
        'Driver Accepted': 'badge-warning',
        'Order Shipped': 'badge-success',
        'In Transit': 'badge-success',
        'Order Completed': 'badge-success',
        'Order Rejected': 'badge-danger',
        'Driver Rejected': 'badge-danger'
    };

    return classMap[dbStatus] || 'badge-secondary';
}
