<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Layout</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background: linear-gradient(to bottom, #800080, #fff);
    }

    .profile-card {
      width: 350px;
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      padding: 20px;
      text-align: center;
    }

    .profile-card img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      margin-bottom: 10px;
    }

    .profile-card h2 {
      margin: 10px 0;
      font-size: 20px;
      font-weight: bold;
    }

    .profile-card .edit-icon {
      float: right;
      font-size: 16px;
      color: gray;
      cursor: pointer;
    }

    .profile-card .details {
      text-align: left;
      margin: 20px 0;
    }

    .profile-card .details div {
      display: flex;
      align-items: center;
      margin: 10px 0;
    }

    .profile-card .details div i {
      margin-right: 10px;
      font-size: 18px;
    }

    .profile-card a {
      text-decoration: none;
      color: #800080;
      font-weight: bold;
    }

    .profile-card .sign-out {
      display: flex;
      justify-content: flex-start;
      align-items: center;
      margin-top: 20px;
      color: #800080;
      cursor: pointer;
    }

    .profile-card .sign-out i {
      margin-right: 10px;
      font-size: 18px;
    }
  </style>
</head>
<body>
  <div class="profile-card">
    <div class="edit-icon">✏️</div>
    <img src="https://via.placeholder.com/80" alt="Profile Picture">
    <h2>Nama Profil</h2>
    <div class="details">
      <div>
        <i>📧</i>
        <a href="mailto:email@gmail.com">email@gmail.com</a>
      </div>
      <div>
        <i>📞</i>
        <span>0895432467</span>
      </div>
    </div>
    <div class="sign-out">
      <i>🔓</i>
      <span>Sign Out</span>
    </div>
  </div>
</body>
</html>
