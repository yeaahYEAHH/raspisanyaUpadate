<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css" />
    <title>Авторизация</title>
  </head>
  <body class="text-center">
    <form method="POST" action="admin.php" id="auth">
        <input type="text" id="login" name="login" placeholder="Логин" class="form-control" pattern="^[a-zA-Z0-9]+$" required>
        <input type="text " name="password" placeholder="Пароль" class="form-control" pattern="^[a-zA-Z0-9]+$" required>
        <button type="submit" class="btn btn-primary">Войти</button>
    </form>
  </body>
</html>