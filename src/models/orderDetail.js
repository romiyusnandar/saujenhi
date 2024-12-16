const db = require("../config/database")

const addOrderDetail = (orderId, productId, quantity, price) => {
  const sql = `INSERT INTO order_details (order_id, product_id, quantity, price)
              VALUES (${orderId}, ${productId}, ${quantity}, ${price})`

  return db.execute(sql)
}

const orderDetailByOrderId = (orderId) => {
  const sql = `SELECT * FROM order_details WHERE order_id = ${orderId}`

  return db.execute(sql)
}

module.exports = {
  addOrderDetail,
  orderDetailByOrderId,
}