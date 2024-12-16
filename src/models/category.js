const db = require("../config/database")

const getAllCategory = async () => {
  const sql = "SELECT * FROM categories"

  return db.execute(sql)
}

const getCategoryById = async (categoryId) => {
  const sql = `SELECT * FROM categories WHERE id = ${categoryId}`

  return db.execute(sql)
}

const createCategory = async (name) => {
  const sql = `INSERT INTO categories (name) VALUES ('${name}')`

  return db.execute(sql)
}

const deleteCategory = async (categoryId) => {
  const sql = `DELETE FROM categories WHERE id = ${categoryId}`

  return db.execute(sql)
}

module.exports = {
  getAllCategory,
  getCategoryById,
  createCategory,
  deleteCategory,
}