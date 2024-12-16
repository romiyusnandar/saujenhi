const express = require("express")
const {
  getStores,
  createStore
} = require("../controller/store")
const authMiddleware = require("../middleware/auth")

const router = express.Router()

router.get("/stores", getStores)
router.post("/stores", authMiddleware, createStore)

module.exports = router