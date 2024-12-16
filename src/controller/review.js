const response = require("../utils/response")
const reviewModel = require("../models/review")

const createProductReview = async (req, res) => {
  try {
    const { productId } = req.params
    const { userId, comment, rating } = req.body
    await reviewModel.addReview(userId, productId, comment, rating)
    response(201, "Berhasil memberikan ulasan", {userId, productId, comment, rating}, res)
  } catch (error) {
    console.error(error)
    response(500, "Internal server error", error, res)
  }
}

const getProductReviews = async (req, res) => {
  try {
    const {productId} = req.params
    const [reviews] = await reviewModel.getReviewsByProductId(productId)
    if (!reviews.length) return response(404, "Belum ada ulasan", null, res)
    response(200, "Menampilkan ulasan produk", reviews, res)
  } catch (error) {
    console.error(error)
    response(500, "Internal server error", null, res)
  }
}

const getReviewByUser = async (req, res) => {
  try {
    const { userId } = req.params
    const [reviews] = await reviewModel.getReviewByUser(userId)
    if (!reviews.length) return response(404, "Belum ada ulasan", null, res)
    response(200, "Menampilkan ulasan user", reviews, res)
  } catch (error) {
    console.error(error)
    response(500, "Internal server error", null, res)
  }
}

const updateProductReview = async (req, res) => {
  try {
    const { reviewId } = req.params
    const updateData = req.body
    const result = await reviewModel.updateReview(reviewId, updateData)
    if (result.affectedRows > 0) {
      response(200, "Berhasil update ulasan", { reviewId,...updateData }, res)
    } else {
      response(404, "Ulasan tidak ditemukan", null, res)
    }
  } catch (error) {
    console.error(error)
    response(500, "Internal server error", null, res)
  }
}

const deleteProductReview = async (req, res) => {
  try {
    const { reviewId } = req.params
    const userId = req.user.id

    const [review] = await reviewModel.getReviewById(reviewId)
    console.log('User ID from token:', userId)
    console.log('Review:', review)
    if (!review || review.length === 0) {
      return response(401, "Ulasan tidak ditemukan", null, res)
    }

    const reviews = review[0]
    if (reviews.user_id !== userId) {
      console.log('User ID from review:', review.user_id)
      return response(401, "Hanya user yang memiliki hak menghapus ulasan dapat melakukan ini", null, res)
    }

    await reviewModel.deleteReview(reviewId)
    response(200, "Berhasil menghapus ulasan", { reviewId }, res)
  } catch (error) {
    console.error(error)
    response(500, "Internal server error", null, res)
  }
}

module.exports = {
  createProductReview,
  getProductReviews,
  getReviewByUser,
  updateProductReview,
  deleteProductReview,
}