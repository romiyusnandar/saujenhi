const userModel = require("../models/users")
const response = require("../utils/response")
const { generateToken } = require("../utils/auth")
const bcrypt = require('bcrypt')

const comparePassword = async (password, hashedPassword) => {
  return await bcrypt.compare(password, hashedPassword)
}

const login = async (req, res) => {
  try {
    const { email, password } = req.body
    const [users] = await userModel.getUserByEmail(email)
    const user = users[0]

    if (!user) {
      return response(401, "Invalid email atau password", null, res)
    }

    const isValidPassword = await comparePassword(password, user.password)
    if (!isValidPassword) {
      return response(401, "Invalid email atau password", null, res)
    }

    const token = generateToken(user)
    console.log("User token", token)
    response(200, "Login berhasil", { token }, res)
  } catch (error) {
    console.error(error)
    response(500, "Internal Server Error", null, res)
  }
}

const register = async (req, res) => {
  try {
    const {body} = req

    const [existingUsers] = await userModel.getUserByEmail(body.email)
    if (existingUsers.length > 0) {
      return response(400, "Email sudah terdaftar", null, res)
    }

    await userModel.createUser(body)
    response(201, "Register berhasil", body, res)
  } catch (error) {
    console.error('Error in Register:', error);
    response(500, "Internal server error", null, res)
  }
}

const logout = (req, res) => {
  req.logout()
  response(200, "Logout berhasil", null, res)
}

module.exports = {
  login,
  register,
  logout
}