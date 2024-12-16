const jwt = require("jsonwebtoken")
const response = require("../utils/response")

const JWT_SECRET = "HBhbihbHB87YbhHB"
const authMiddleware = (req, res, next) => {
  try {
    const authHeader = req.headers.authorization
    console.log("Authorization header:", authHeader)
    if (!authHeader) {
      return response(401, "Akses ditolak karena token tidak valid", null, res);
    }

    const token = authHeader.split(" ")[1]
    console.log("Token:", token)
    if (!token) {
      return response(401, "Akses ditolak karena token yang diberikan invalid", null, res)
    }

    jwt.verify(token, JWT_SECRET, (err, decoded) => {
      if (err) {
        if (err.name === "TokenExpiredError") {
          return response(401, "Akses ditolak karena token sudah kadaluarsa", null, res)
        }
        return response(401, "Akses ditolak karena token yang diberikan invalid", null, res)
      }
      req.user = decoded
      next()
    })
  } catch (error) {
    console.error(error)
    return response(500, "Internal server error", null, res)
  }
}

module.exports = authMiddleware