const response = require("../utils/response")
const storeModel = require("../models/store")

const getStores = async (req, res) => {
  try {
    const [stores] = await storeModel.getAllStore()
    response(200, "Mendapatkan data toko", stores, res)
  } catch (error) {
    console.error(error)
    response(500, "Error retrieving stores", null, res)
  }
}

const createStore = async (req, res) => {
  try {
    const {body} = req
    const userId = req.user.id
    await storeModel.createStore(body, userId)
    response(201, "Berhasil membuat toko", body, res)
  } catch (error) {
    console.error(error)
    response(500, "Error creating store", null, res)
  }
}
module.exports = {
  getStores,
  createStore
}