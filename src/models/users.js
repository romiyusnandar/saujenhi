const db = require("../config/database")
const bcrypt = require('bcrypt')

const getAllUsers = () => {
  const sql = "SELECT * FROM users"

  return db.execute(sql)
}

const getUserById = (id) => {
  const sql = `SELECT * FROM users WHERE id = ${id}`

  return db.execute(sql)
}

const getUserByEmail = (email) => {
  const sql = `SELECT * FROM users WHERE email = '${email}'`
  return db.execute(sql)
}

const createUser = async (body) => {
  // Hash password
  const saltRounds = 10
  const hashedPassword = await bcrypt.hash(body.password, saltRounds)

  const sql = `INSERT INTO users (username, email, password, role, address)
              VALUES ('${body.username}', '${body.email}', '${hashedPassword}', '${body.role}', '${body.address}')`

              return db.execute(sql)
}

const updateUser = async (id, body) => {
  // Hash password
  if (body.password) {
    const saltRounds = 10
    body.password = await bcrypt.hash(body.password, saltRounds)
  }

  const updateFields = []
  const updateValues = []

  for (const [key, value] of Object.entries(body)) {
    if (value !== undefined) {
      updateFields.push(`${key} = ?`)
      updateValues.push(value)
    }
  }

  updateValues.push(id)

  const sql = `UPDATE users SET ${updateFields.join(', ')} WHERE id = ?`

  return db.execute(sql, updateValues)
}

const deleteUser = (id) => {
  const sql = `DELETE FROM users WHERE id = ${id}`

  return db.execute(sql)
}


module.exports = {
  getAllUsers,
  getUserById,
  getUserByEmail,
  createUser,
  updateUser,
  deleteUser
}