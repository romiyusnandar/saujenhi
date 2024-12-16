const express = require("express")
// const dotenv = require('dotenv')
const userRoutes = require("./routes/users")
const storeRoutes = require("./routes/store")
const authRoutes= require("./routes/auth")
const categoryRoutes = require("./routes/category")
const productReoutes = require("./routes/products")
const cartRoutes = require("./routes/cart")
const reviewRoutes = require("./routes/review")
const orderRoutes = require("./routes/order")
const logging = require("./middleware/logs")

require('dotenv').config();
// dotenv.config()
const app = express()

app.use(logging)
app.use(express.json())
app.use(userRoutes)
app.use(storeRoutes)
app.use(authRoutes)
app.use(categoryRoutes)
app.use(productReoutes)
app.use(cartRoutes)
app.use(reviewRoutes)
app.use(orderRoutes)

app.use("/uploads", express.static("public/uploads"))
app.get("/", (req, res) => {
  res.json(200, [
    {
      status: 200,
      msg: "Welcome to saujenhi API 🚀",
      author: "Romiz"
    }
  ])
})

const port = process.env.PORT
app.listen(parseInt(port), () => {
  console.log(`Server is running on port ${port}`)
})