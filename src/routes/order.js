const express = require("express")
const router = express.Router()
const {
  createOrder,
  getOrderById,
  updateOrderStatus,
  updateShippingStatus,
  getOrdersByUserId
} = require("../controller/oder")

router.post("/order", createOrder)
router.get("/order/:orderId", getOrderById)
router.patch("/order/:orderId/status", updateOrderStatus)
router.patch("/order/:orderId/shipping-status", updateShippingStatus)
router.get("/orders/user/:userId", getOrdersByUserId)

module.exports = router