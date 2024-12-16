const transactionModel = require("../models/order");
const response = require("../utils/response");
const midtransClient = require('midtrans-client');
require('dotenv').config();

const snap = new midtransClient.Snap({
  isProduction : false,
  serverKey : `${process.env.SERVER_KEY}`
});

console.log('Server Key:', process.env.SERVER_KEY);

console.log('PORT:', process.env.PORT);
console.log('SERVER_KEY:', process.env.SERVER_KEY);
console.log('PUBLIC_CLIENT:', process.env.PUBLIC_CLIENT);
console.log('PUBLIC_AP:', process.env.PUBLIC_AP);

const createTransaction = async (req, res) => {
  try {
    const { products, customer_name, customer_email } = req.body

    // Validasi produk
    if (!products || products.length === 0) {
      return response(400, "Products tidak valid", null, res);
    }

    const random = Math.floor(Math.random() * 1000000);
    // Generate transaksi ID dan hitung total gross_amount
    const transaction_id = `TRX-${random}`;
    const gross_amount = products.reduce((acc, product) => acc + (product.quantity * product.price), 0);

    // Buat parameter untuk Midtrans
    let parameter = {
      transaction_details: {
        order_id: transaction_id,
        gross_amount: gross_amount
      },
      customer_details: {
        first_name: customer_name,
        email: customer_email
      },
      item_details: products.map(product => ({
        id: product.id,
        price: product.price,
        quantity: product.quantity,
        name: product.name
      }))
    };


    // Buat transaksi di Midtrans
    const transaction = await snap.createTransaction(parameter);
    console.log("Transaction result:", transaction.token);
    // Simpan transaksi dan item transaksi
    await transactionModel.createTransaction({
      gross_amount,
      customer_name,
      customer_email,
      transaction_id,
      snap_token: transaction.token
    });

    await transactionModel.createTransactionItems({
      transaction_id,
      products,
    });

    response(201, "Transaksi berhasil dibuat", {
      transaction_id: transaction_id,
      status: "PENDING_PAYMENT",
      customer_name,
      customer_email,
      products,
      snap_token: transaction.token,
      redirect_url: transaction.redirect_url,
    }, res);
  } catch (error) {
    console.error(error);
    response(500, "Error creating transaction", null, res);
  }
};

const getTransactions = async (req, res) => {
  try {
    const { status } = req.query;
    const [transactions] = await transactionModel.getTransactions({ status });

    response(200, "Transaksi ditemukan", transactions, res);
  } catch (error) {
    console.error(error);
    response(500, "Error getting transactions", null, res);
  }
};

const getTransactionById = async (req, res) => {
  try {
    const { transaction_id } = req.params;
    const [transaction] = await transactionModel.getTransactionById({ transaction_id });

    if (!transaction || transaction.length === 0) {
      return response(404, "Transaksi tidak ditemukan", null, res);
    }

    response(200, "Transaksi ditemukan", transaction[0], res);
  } catch (error) {
    console.error(error);
    response(500, "Error getting transaction", null, res);
  }
};

const updateTransactionStatus = async (req, res) => {
  try {
    const { transaction_id } = req.params;
    const { status, payment_method } = req.body;

    await transactionModel.updateTransactionStatus({ transaction_id, status, payment_method });
    response(200, "Status transaksi berhasil diperbarui", { transaction_id, status }, res);
  } catch (error) {
    console.error(error);
    response(500, "Error updating transaction status", null, res);
  }
};

module.exports = {
  createTransaction,
  getTransactions,
  getTransactionById,
  updateTransactionStatus,
};
