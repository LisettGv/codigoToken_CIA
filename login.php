<?php
// Procesamiento
$msg = "";
$valid = false;
$remaining = 0;
$claims = [];

function b64url_decode($data){
  $repl = ['-'=>'+','_'=>'/'];
  $data = strtr($data, $repl);
  return base64_decode($data . str_repeat('=', (4 - strlen($data) % 4) % 4));
}

function validar_jwt_hs256($jwt, $secret){
  $parts = explode('.', $jwt);
  if(count($parts) !== 3) return [false, "Formato JWT inválido", null];
  [$h, $p, $s] = $parts;

  // Decodificar header y payload
  $header = json_decode(b64url_decode($h), true);
  $payload = json_decode(b64url_decode($p), true);
  if(!$header || !$payload) return [false, "Header o payload inválidos", null];

  // Algoritmo esperado
  if(($header['alg'] ?? '') !== 'HS256') return [false, "Algoritmo no soportado", null];

  // Verificar firma
  $calc = rtrim(strtr(base64_encode(hash_hmac('sha256', "$h.$p", $secret, true)), '+/', '-_'), '=');
  if($calc !== $s) return [false, "Firma inválida", null];

  // Verificar expiración
  $now = time();
  $exp = $payload['exp'] ?? 0;
  if($exp <= $now) return [false, "Token expirado", $payload];

  return [true, "Token válido", $payload];
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $email = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');
  $token = trim($_POST['token'] ?? '');

  // Regla simple de credenciales: password debe ser 1234
  if($password !== '1234'){
    $msg = "Credenciales incorrectas.";
  } else {
    // Validar JWT
    $SECRET_KEY = "clave_secreta_super_segura";
    [$ok, $m, $payload] = validar_jwt_hs256($token, $SECRET_KEY);
    if(!$ok){
      $msg = "Error JWT: $m";
    } else {
      $valid = true;
      $claims = $payload;
      $remaining = max(0, ($payload['exp'] ?? 0) - time());
      $msg = "Inicio de sesión exitoso.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Resultado de login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family:Arial;display:flex;justify-content:center;align-items:center;height:100vh;background:#f7f9fc}
    .card{background:#fff;padding:22px;border-radius:10px;box-shadow:0 6px 18px rgba(31,45,61,.06);width:460px}
    .msg{margin-bottom:10px}
    .ok{color:#155724;background:#d4edda;padding:10px;border-radius:6px}
    .err{color:#721c24;background:#f8d7da;padding:10px;border-radius:6px}
    .timer{margin:8px 0;color:#555}
    .barWrap{height:8px;background:#eee;border-radius:6px;overflow:hidden}
    .bar{height:8px;background:#28a745;width:0%}
    a.btn{display:inline-block;margin-top:10px;padding:10px 12px;background:#2b7cff;color:#fff;text-decoration:none;border-radius:6px}
    .row{margin-top:20px}
  </style>
</head>
<body>
  <div class="card">
    <h3>Resultado</h3>

    <?php
      if($msg){
        $cls = $valid ? "ok" : "err";
        echo "<div class='msg $cls'>$msg</div>";
      }
    ?>

    <?php if($valid): ?>
      <div class="timer">
        Token expira en: <strong><span id="secLeft"><?php echo (int)$remaining; ?></span> s</strong>
      </div>
      <div class="barWrap"><div id="bar" class="bar"></div></div>
    <?php else: ?>
      <p>Revisa que la contraseña sea <strong>1234</strong> y pega el <strong>JWT completo</strong> de token.txt.</p>
    <?php endif; ?>

    <div class="row">
      <a class="btn" href="index.php">Volver</a>
    </div>
  </div>

  <script>
    const ttl = <?php echo (int)$remaining; ?>; // segundos restantes desde exp (0 si no válido)
    let remaining = ttl;
    const bar = document.getElementById('bar');
    const secLeft = document.getElementById('secLeft');

    function updateBar() {
      if(!bar || ttl === 0) return;
      const pct = Math.max(0, Math.min(100, (remaining/ttl)*100));
      bar.style.width = pct + '%';
      bar.style.background = remaining > ttl*0.5 ? '#28a745' :
                             (remaining > ttl*0.2 ? '#ffc107' : '#dc3545');
    }

    function tick() {
      if(!secLeft || ttl === 0) return;
      if (remaining > 0) {
        remaining--;
        secLeft.textContent = remaining;
        updateBar();
      } else {
        const cont = document.querySelector('.msg.ok');
        if(cont){
          cont.textContent = 'Token expirado. Vuelve a generar uno en Python.';
          cont.className = 'msg err';
        }
      }
    }

    updateBar();
    setInterval(tick, 1000);
  </script>
</body>
</html>

