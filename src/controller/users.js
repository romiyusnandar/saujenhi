const response = require("../utils/response")
const userModel = require("../models/users")

const getAllUsers = async (req, res) => {
  try {
    const [data] = await userModel.getAllUsers()
    response(200, "Mendapatkan semua user", data, res)
  } catch (error) {
    console.error('Error in getAllUsers:', error);
    response(500, "Internal server error", null, res)
  }
}

const getUserById = async (req, res) => {
  try {
    const {id} = req.params
    const [data] = await userModel.getUserById(id)
    if (!data.length) {
      return response(404, "User tidak ditemukan!", null, res)
    }
    response(200, "Mendapatkan user berdasarkan ID", data, res)
  } catch (error) {
    console.error('Error in getUserById:', error);
    response(500, "Internal server error", null, res)
  }
}

const getUserByEmail = async (req, res) => {
  try {
    const {email} = req.params
    const [data] = await userModel.getUserByEmail(email)
    if (data.length === 0) {
      return response(404, "User tidak ditemukan!", null, res)
    }

    response(200, "Mendapatkan user berdasarkan email", data, res)
  } catch (error) {
    console.error('Error in getUserByEmail:', error);
    response(500, "Internal server error", null, res)
  }
}

const createUser = async (req, res) => {
  try {
    const {body} = req
    await userModel.createUser(body)
    response(201, "User berhasil ditambahkan", body, res)
  } catch (error) {
    console.error('Error in createUser:', error);
    response(500, "Internal server error", null, res)
  }
}

const updateUser = async (req, res) => {
  try {
    const {id} = req.params
    const {body} = req
    await userModel.updateUser(id, body)
    response(200, "User berhasil diperbarui", {id: id, ...body}, res)
  } catch (error) {
    console.error('Error in updateUser:', error);
    response(500, "Internal server error", null, res)
  }
}

const deleteUser = async (req, res) => {
  try {
    const {id} = req.params
    await userModel.deleteUser(id)
    response(200, "User berhasil dihapus", {id: id}, res)
  } catch (error) {
    console.error('Error in deleteUser:', error);
    response(500, "Internal server error", null, res)
  }
}

module.exports = {
  getAllUsers,
  getUserById,
  getUserByEmail,
  createUser,
  updateUser,
  deleteUser
}