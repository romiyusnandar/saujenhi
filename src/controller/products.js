const response = require("../utils/response")
const productModel = require("../models/products")

const getAllProducts = async (req, res) => {
  try {
    const [products] = await productModel.getAllProducts()
    if (!products.length) return response(404, "Data produk kosong", null, res)
    response(200, "Data berhasil ditemukan", products, res)
  } catch (error) {
    response(500, "Internal server error", null, res)
  }
}

const getProductById = async (req, res) => {
  try {
    const { id } = req.params
    const [product] = await productModel.getProductById(id)
    if (!product.length) return response(404, "Produk dengan id tersebut tidak ditemukan", null, res)
    response(200, "Data produk berhasil ditemukan", product, res)
  } catch (error) {
    console.error(error)
    response(500, "Internal server error", null, res)
  }
}

const createProduct = async (req, res) => {
  try {
    const newProduct = req.body
    const [createdProduct] = await productModel.createProduct(newProduct)

    if (!newProduct.name || !newProduct.price || !newProduct.store_id || !newProduct.category_id) {
      return response(400, "Nama produk, harga, store_id, dan category_id harus diisi", null, res)
    }

    if (isNaN(newProduct.price) || newProduct.price <= 0) {
      return response(400, "Harga produk harus berupa angka positif", null, res)
    }
    response(201, "Produk baru berhasil ditambahkan", newProduct, res)
  } catch (error) {
    console.error(error)
    response(500, "Internal server error", null, res)
  }
}

const updateProduct = async (req, res) => {
  try {
    const { id } = req.params
    const updateData = req.body

    const result = await productModel.updateProduct(id, updateData)

    if (result.affectedRows > 0) {
      const [updatedProduct] = await productModel.getProductById(id)
      if (updatedProduct.length > 0) {
        response(200, "Data produk berhasil diperbarui", updatedProduct[0], res)
      } else {
        response(404, "Produk tidak ditemukan setelah update", null, res)
      }
    } else {
      response(404, "Produk tidak ditemukan atau tidak ada perubahan", null, res)
    }
  } catch (error) {
    console.error("Error in updateProduct:", error)
    response(500, "Internal server error", null, res)
  }
}

const deleteProduct = async (req, res) => {
  try {
    const { id } = req.params
    await productModel.deleteProduct(id)
    response(200, "Produk berhasil dihapus", {id: id}, res)
  } catch (error) {
    console.error("Error in deleteProduct:", error)
    response(500, "Internal server error", null, res)
  }
}

module.exports = {
  getAllProducts,
  getProductById,
  createProduct,
  updateProduct,
  deleteProduct
}