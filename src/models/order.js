const db = require("../config/database");

const createTransaction = async ({ gross_amount, customer_name, customer_email, snap_token = null, snap_redirect_url = null, transaction_id }) => {
  const sql = `INSERT INTO orders (total_amount, status, customer_name, customer_email, snap_token, snap_redirect_url, transaction_id)
               VALUES (${gross_amount}, 'pending', '${customer_name}', '${customer_email}', '${snap_token}', '${snap_redirect_url}', '${transaction_id}')`;
  return db.execute(sql);
};

const createTransactionItems = async ({ products, transaction_id }) => {
  const values = products.map((product) => {
    const random = Math.random()
    const itemId = `TRX-ITEM-${random}`;
    return `('${transaction_id}', '${product.id}', ${product.price}, ${product.quantity})`;
  }).join(", ");

  const sql = `INSERT INTO order_details (order_id, product_id, price, quantity)
               VALUES ${values}`;
  return db.execute(sql);
};

const getTransactions = async ({ status }) => {
  let sql = `SELECT * FROM transactions`;
  if (status) {
    sql += ` WHERE status = '${status}'`;
  }
  return db.execute(sql);
};

const getTransactionById = async ({ transaction_id }) => {
  const sql = `SELECT * FROM transactions WHERE id = '${transaction_id}'`;
  return db.execute(sql);
};

const updateTransactionStatus = async ({ transaction_id, status, payment_method = null }) => {
  const sql = `UPDATE transactions SET status = '${status}', payment_method = '${payment_method}' WHERE id = '${transaction_id}'`;
  return db.execute(sql);
};

module.exports = {
  createTransaction,
  createTransactionItems,
  getTransactions,
  getTransactionById,
  updateTransactionStatus,
};
