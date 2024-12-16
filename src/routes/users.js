const express = require("express")
const router = express.Router()
const {
  getAllUsers,
  getUserById,
  getUserByEmail,
  createUser,
  updateUser,
  deleteUser
} = require("../controller/users")

router.get("/users", getAllUsers)
router.get("/users/:id", getUserById)
router.get("/users/email/:email", getUserByEmail)
router.post("/users", createUser)
router.patch("/users/:id", updateUser)
router.delete("/users/:id", deleteUser)

module.exports = router