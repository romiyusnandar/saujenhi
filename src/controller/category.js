const response = require("../utils/response")
const categoryModel = require("../models/category")

const getAllCategories = async (req, res) => {
  try {
    const [categories] = await categoryModel.getAllCategory()
    response(200, "Data kategori", categories, res)
  } catch (error) {
    console.error(error)
    response(500, "Gagal mengambil data kategori", error, res)
  }
}

const getCategoryById = async (req, res) => {
  try {
    const { id } = req.params
    const [category] = await categoryModel.getCategoryById(id)

    if (!category) {
      response(404, "Kategori dengan ID tersebut tidak ditemukan", {}, res)
      return
    }
    response(200, "Data kategori", category, res)
    } catch (error) {
    console.error(error)
    response(500, "Gagal mengambil data kategori", error, res)
  }
}

const createCategory = async (req, res) => {
  try {
    const { name } = req.body
    await categoryModel.createCategory(name)
    response(201, "Kategori berhasil ditambahkan", {name: name}, res)
  } catch (error) {
    console.error(error)
    response(500, "Gagal menambahkan kategori", error, res)
  }
}

const deleteCategory = async (req, res) => {
  try {
    const { id } = req.params
    await categoryModel.deleteCategory(id)
    response(200, "Kategori berhasil dihapus", {id: id}, res)
  } catch (error) {
    console.error(error)
    response(500, "Gagal menghapus kategori", error, res)
  }
}

module.exports = {
  getAllCategories,
  getCategoryById,
  createCategory,
  deleteCategory,
}