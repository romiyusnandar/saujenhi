const response = (status_code, message, datas, res) => {
  res.json(status_code, [
    {
      status: status_code,
      msg: message,
      data: datas
    }
  ])
}

module.exports = response