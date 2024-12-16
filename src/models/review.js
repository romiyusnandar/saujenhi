const db = require("../config/database")

const addReview = (userId, productId, comment, rating) => {
  const sql = `INSERT INTO reviews (user_id, product_id, comment, rating) VALUES (?,?,?,?)`
  const values = [userId, productId, comment, rating]

  return db.execute(sql, values)
}

const getReviewByUser = (userId) => {
  const sql = `SELECT * FROM reviews WHERE user_id =?`
  const values = [userId]

  return db.execute(sql, values)
}

const getReviewById = (reviewId) => {
  const sql = `SELECT * FROM reviews WHERE id =?`
  const values = [reviewId]
  return db.execute(sql, values)
}
const getReviewsByProductId = (productId) => {
  const sql = `SELECT * FROM reviews WHERE product_id =?`
  const values = [productId]

  return db.execute(sql, values)
}

const updateReview = async (reviewId, body) => {
  const updateFields = []
  const updateValues = []

  for (const [key, value] of Object.entries(body)) {
    if (value !== undefined) {
      updateFields.push(`${key} = ?`)
      updateValues.push(value)
    }
  }

  if (updateFields.length === 0) {
    throw new Error('No fields to update')
  }

  updateValues.push(reviewId)

  const sql = `UPDATE reviews SET ${updateFields.join(', ')} WHERE id = ?`

  try {
    const [result] = await db.execute(sql, updateValues)
    return result
  } catch (error) {
    console.error('Error updating review:', error)
    throw error
  }
}

const deleteReview = (reviewId) => {
  const sql = `DELETE FROM reviews WHERE id =?`
  const values = [reviewId]

  return db.execute(sql, values)
}
module.exports = {
  addReview,
  updateReview,
  getReviewsByProductId,
  getReviewByUser,
  getReviewById,
  deleteReview
}