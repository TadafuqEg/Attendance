<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Account Deletion</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: Arial, sans-serif;
  background-color: #f4f4f4;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.container {
  background-color: white;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  text-align: center;
  width: 300px;
}

h1 {
  color: #e74c3c;
}

label {
  display: block;
  margin-bottom: 10px;
  font-weight: bold;
}

input {
  width: 100%;
  padding: 8px;
  margin-bottom: 20px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

button {
  width: 100%;
  padding: 10px;
  background-color: #e74c3c;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
}

button:hover {
  background-color: #c0392b;
}

.result {
  margin-top: 20px;
  font-size: 16px;
  color: #2c3e50;
}
  </style>
</head>
<body>
  <div class="container">
    <h1>Delete Account</h1>
    <form id="delete-form">
      <label for="username">Enter Username:</label>
      <input type="text" id="username" name="username" required>
      <button type="submit">Delete Account</button>
    </form>
    <div id="result" class="result"></div>
  </div>

  <script>
    document.getElementById('delete-form').addEventListener('submit', function(event) {
      event.preventDefault();
      const username = document.getElementById('username').value;
      const result = document.getElementById('result');
      
      if (username) {
        result.innerHTML = `<p>Account and associated data for <strong>${username}</strong> will be deleted after 7 days.</p>`;
      } else {
        result.innerHTML = `<p>Please enter a valid username.</p>`;
      }
    });
  </script>
</body>
</html>

