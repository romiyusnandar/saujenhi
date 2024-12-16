const express = require("express")
const authMiddleware = require("../middleware/auth")
const router = express.Router()
const {
  createProductReview,
  getProductReviews,
  getReviewByUser,
  updateProductReview,
  deleteProductReview,
} = require("../controller/review")

router.get("/products/:productId/review", getProductReviews)
router.get("/users/:userId/review", getReviewByUser)
router.post("/products/:productId/review", createProductReview)
router.patch("/products/:productId/review/:reviewId", updateProductReview)
router.delete("/products/:productId/review/:reviewId", authMiddleware, deleteProductReview)

module.exports = router