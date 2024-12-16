const response = require("../utils/response")
const orderModel = require("../models/order")
const orderDetailModel = require("../models/orderDetail")

const createOrder = async (req, res) => {
  try {
    const { userId, totalAmount, paymentMethod } = req.body
    const [order] = await orderModel.createOrder(userId, totalAmount, paymentMethod)
    const orderId = order.insertId

    for (const detail of detail.length > 0) {
      await orderDetailModel.createOrder(orderId, detail.productId, detail.quantity, detail.price)
    }

    response(201, "Order berhasil dibuat", order, res)
  } catch (error) {
    console.error(error)
    response(500, "Error creating order", null, res)
  }
}

const getOrderById = async (req, res) => {
  try {
    const { orderId } = req.params
    const [orders] = await orderModel.getOrderById(orderId)
    if (!orders || orders.length === 0) {
      return response(404, "Order tidak ditemukan", null, res)
    }

    const [orderDetails] = await orderDetailModel.orderDetailByOrderId(orderId)
    response(200, "Order ditemukan", {order: orders, orderDetails}, res)
  } catch (error) {
    console.error(error)
    response(500, "Error getting orders", null, res)
  }
}

const updateOrderStatus = async (req, res) => {
  try {
    const { orderId } = req.params
    const { status } = req.body

    await orderModel.updateOrderStatus(orderId, status)
    response(200, "Status order berhasil diubah", {orderId, status}, res)
  } catch (error) {
    console.error(error)
    response(500, "Error updating order status", null, res)
  }
}

const updateShippingStatus = async (req, res) => {
  try {
    const { orderId } = req.params
    const { shippingStatus } = req.body

    await orderModel.updateShippingStatus(orderId, shippingStatus)
    response(200, "Status pengiriman berhasil diperbarui", {orderId, shippingStatus}, res)
  } catch (error) {
    console.error(error)
    response(500, "Error updating shipping status", null, res)
  }
}

const getOrdersByUserId = async (req, res) => {
  try {
    const { userId } = req.params
    const [orders] = await orderModel.getOrdersByUserId(userId)
    if (orders.length === 0) {
      return response(404, "Belum ada order yang dilakukan", null, res)
    }
    response(200, "Orders ditemukan", orders, res)
  } catch (error) {
    console.error(error)
    response(500, "Error getting orders", null, res)
  }
}

module.exports = {
  createOrder,
  getOrderById,
  updateOrderStatus,
  updateShippingStatus,
  getOrdersByUserId,
}