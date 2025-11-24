<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login con JWT</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family:Arial;display:flex;justify-content:center;align-items:center;height:100vh;background:#f5f7fb}
    .card{background:#fff;padding:22px;border-radius:10px;box-shadow:0 6px 18px rgba(31,45,61,.06);width:360px}
    input{width:100%;padding:10px;margin:8px 0;border:1px solid #ddd;border-radius:6px}
    button{width:100%;padding:10px;background:#2b7cff;color:#fff;border:0;border-radius:6px;cursor:pointer}
    .small{color:#666;font-size:13px}
  </style>
</head>
<body>
  <div class="card">
    <h3>Iniciar sesión</h3>
    <form action="login.php" method="POST">
      <input type="text" name="email" placeholder="Correo electrónico (cualquiera)" required>
      <input type="password" name="password" placeholder="Contraseña (1234)" required>
      <input type="text" name="token" placeholder="Pega aquí el JWT desde token.txt" required>
      <button type="submit">Ingresar</button>
    </form>
    <p class="small">Ejecuta primero generateToken.py para crear token.txt</p>
  </div>
</body>
</html>
