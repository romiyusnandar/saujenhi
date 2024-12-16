const express = require("express")
const userRoutes = require("./routes/users")
const storeRoutes = require("./routes/store")
const authRoutes= require("./routes/auth")
const categoryRoutes = require("./routes/category")
const productReoutes = require("./routes/products")
const cartRoutes = require("./routes/cart")
const reviewRoutes = require("./routes/review")
const orderRoutes = require("./routes/order")
const logging = require("./middleware/logs")

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

app.listen(4000, () => {
  console.log("Server is running on port 4000")
})