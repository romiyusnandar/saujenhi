const mysql = require("mysql2")

const dbPool = mysql.createPool({
  host: "localhost",
  user: "root",
  database: "meii",
  password: "",
});

module.exports = dbPool.promise()