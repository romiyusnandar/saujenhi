const db = require("../config/database")

const getAllStore = () => {
  const sql = "SELECT * FROM store"

  return db.execute(sql)
}

const createStore = (body, userId) => {
  const sql = `INSERT INTO store (name, description, address, contact, image, created_by)
              VALUES ('${body.name}', '${body.description}', '${body.address}', '${body.contact}', '${body.image}', ${userId})`

  return db.execute(sql)
}

module.exports = {
  getAllStore,
  createStore
}