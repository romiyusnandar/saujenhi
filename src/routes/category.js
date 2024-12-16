const express = require("express")
const router = express.Router()

const {
  getAllCategories,
  getCategoryById,
  createCategory,
  deleteCategory,
} = require("../controller/category")

router.get("/category", getAllCategories)
router.get("/category/:id", getCategoryById)
router.post("/category", createCategory)
router.delete("/category/:id", deleteCategory)

module.exports = router