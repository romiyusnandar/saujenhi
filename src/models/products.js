const db = require("../config/database")

const getAllProducts = async () => {
  const sql = "SELECT * FROM products"

  return db.execute(sql)
}

const getProductById = async (id) => {
  const sql = `SELECT * FROM products WHERE id = ${id}`

  return db.execute(sql)
}

const createProduct = async (body) => {
  const sql = `INSERT INTO products (slug, name, description, price, image, store_id, category_id)
              VALUES ('${body.slug}', '${body.name}', '${body.description}', ${body.price}, '${body.image}', ${body.store_id}, ${body.category_id})`

  return db.execute(sql)
}

const updateProduct = async (id, body) => {
  const updateFields = []
  const values = []

  for (const [key, value] of Object.entries(body)) {
    if (value !== undefined) {
      updateFields.push(`${key} = ?`)
      values.push(value)
    }
  }

  if (updateFields.length === 0) {
    throw new Error('No fields to update')
  }

  const sql = `UPDATE products SET ${updateFields.join(", ")} WHERE id = ?`
  values.push(id)

  try {
    const [result] = await db.execute(sql, values)
    return result
  } catch (error) {
    console.error('Error updating product:', error)
    throw error
  }
}

const deleteProduct = async (id) => {
  const sql = `DELETE FROM products WHERE id = ${id}`

  return db.execute(sql)
}

module.exports = {
  getAllProducts,
  getProductById,
  createProduct,
  updateProduct,
  deleteProduct
}