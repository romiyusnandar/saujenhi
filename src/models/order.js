const db = require("../config/database")

const createOrder = (userId, totalAmount, paymentMethod) => {
  const sql = `INSERT INTO orders (user_id, total_amount, payment_method)
              VALUES (${userId}, ${totalAmount}, '${paymentMethod}')`

  return db.execute(sql)
}

const getOrderById = async (orderId) => {
  const sql = `SELECT * FROM orders WHERE id = ${orderId}`

  return db.execute(sql)
}

const getOrdersByUserId = async (userId) => {
  const sql = `SELECT * FROM orders WHERE user_id = ${userId}`

  return db.execute(sql)
}

const updateOrderStatus = async (orderId, status) => {
  const sql = `UPDATE orders SET status = '${status}' WHERE id = ${orderId}`

  return db.execute(sql)
}

const updateShippingStatus = (orderId, shippingStatus) => {
  const sql = `UPDATE orders SET shipping_status = '${shippingStatus}' WHERE id = ${orderId}`

  return db.execute(sql)
}

const updateTransactionId = (orderId, transactionId) => {
  const sql = `UPDATE orders SET transaction_id = '${transactionId}' WHERE id = ${orderId}`

  return db.execute(sql)
}

module.exports = {
  createOrder,
  getOrderById,
  getOrdersByUserId,
  updateOrderStatus,
  updateShippingStatus,
  updateTransactionId,
}