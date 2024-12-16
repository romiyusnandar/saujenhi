const express = require("express")
const router = express.Router()

const {
  getAllProducts,
  getProductById,
  createProduct,
  updateProduct,
  deleteProduct
} = require("../controller/products")

router.get("/products", getAllProducts)
router.get("/products/:id", getProductById)
router.post("/products", createProduct)
router.patch("/products/:id", updateProduct)
router.delete("/products/:id", deleteProduct)

module.exports = router