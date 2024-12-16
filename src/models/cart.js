const db = require("../config/database")

const addToCart = (userId, productId) => {
  const sql = `INSERT INTO cart (user_id, product_id) VALUES (${userId}, ${productId})`

  return db.execute(sql)
}

const getCartByUser = (userId) => {
  const sql = `
    SELECT c.id, c.user_id, c.product_id, c.quantity, p.name, p.price, p.image
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
  `
  const value = [userId]

  return db.execute(sql, value)
}

const updateCartQuantity = (cartId, quantity) => {
  const sql = `UPDATE cart SET quantity = ? WHERE id = ?`;
  return db.execute(sql, [quantity, cartId]);
};

const deleteCartItem = (userId, cartId) => {
  const sql = `DELETE FROM cart WHERE user_id = ${userId} AND id = ${cartId}`;

  return db.execute(sql)
}

module.exports = {
  addToCart,
  getCartByUser,
  updateCartQuantity,
  deleteCartItem,
}