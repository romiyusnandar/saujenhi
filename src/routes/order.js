const express = require("express")
const router = express.Router()
const {
  createTransaction,
  getTransactions,
  getTransactionById,
  updateTransactionStatus
} = require("../controller/oder")

router.post("/order", createTransaction)
router.get("/order", getTransactions)
router.get("/order/:transaction_id", getTransactionById)
router.patch("/order/:transaction_id", updateTransactionStatus)

module.exports = router