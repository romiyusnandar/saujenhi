const response = require("../utils/response")
const cartModel = require("../models/cart")

const createCartItem = async (req, res) => {
  try {
    const { userId, productId } = req.body
    await cartModel.addToCart(userId, productId)
    response(201, "Barang berhasil ditambahkan ke keranjang", productId, res)
  } catch (error) {
    console.error(error)
    response(500, "Internal server error", error, res)
  }
}

const getCartItemByUserId = async (req, res) => {
  try {
    const { userId } = req.params
    const [cartItems] = await cartModel.getCartByUser(userId)
    if (!cartItems.length) {
      response(404, "Keranjang kosong", {}, res)
      return
    }
    response(200, "Daftar barang dalam keranjang", cartItems, res)
  } catch (error) {
    console.error(error)
    response(500, "Internal server error", error, res)
  }
}

const updateCartItemQuantity = async (req, res) => {
  try {
    const { itemId } = req.params
    const { quantity } = req.body
    await cartModel.updateCartQuantity(itemId, quantity)
    response(200, "Quantity barang dikeranjang berhasil diubah", {id: itemId, quantity}, res)
  } catch (error) {
    console.error(error)
    response(500, "Internal server error", error, res)
  }
}

const deleteCartItem = async (req, res) => {
  try {
    const { userId, itemId } = req.params
    await cartModel.deleteCartItem(userId, itemId)
    response(200, "Barang berhasil dihapus dari keranjang", {id: itemId}, res)
  } catch (error) {
    console.error(error)
    response(500, "Internal server error", error, res)
  }
}

module.exports = {
  createCartItem,
  getCartItemByUserId,
  updateCartItemQuantity,
  deleteCartItem,
}