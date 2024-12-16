const express = require("express")
const {
  createCartItem,
  getCartItemByUserId,
  updateCartItemQuantity,
  deleteCartItem
} = require("../controller/cart")
const router = express.Router()

router.get("/cart/:userId", getCartItemByUserId)
router.post("/cart/:userId", createCartItem)
router.patch("/cart/:userId/:itemId", updateCartItemQuantity)
router.delete("/cart/:userId/:itemId", deleteCartItem)

module.exports = router