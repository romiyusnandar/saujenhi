const jwt = require("jsonwebtoken")
const bcrypt = require('bcrypt')

const JWT_SECRET = "HBhbihbHB87YbhHB"

const generateToken = (user) => {
  return jwt.sign(
    {
      id: user.id,
      username: user.username,
      email: user.email,
      role: user.role
    },
    JWT_SECRET,
    { expiresIn: "1d" })
}

const comparePasswords = async (plainTextPassword, hashedPassword) => {
  return await bcrypt.compare(plainTextPassword, hashedPassword)
}

const hashPassword = async (password) => {
  const salt = await bcrypt.genSalt(10)
  return await bcrypt.hash(password, salt)
}

module.exports = {
  generateToken,
  comparePasswords,
  hashPassword
}