<?php
/**
 * СИСТЕМА АВТОРИЗАЦИИ — Агентство "Алтын Ачкыч"
 */
session_start();

// Конфигурация доступа (в реальных системах данные берутся из DB)
$admin_user = "admin";
$admin_pass = "admin777"; // Пароль для диплома

$error = "";

if (isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    if ($user === $admin_user && $pass === $admin_pass) {
        $_SESSION['auth'] = true;
        $_SESSION['user'] = $user;
        header("Location: index.html"); // Переход на главную после входа
        exit;
    } else {
        $error = "Неверный логин или пароль!";
    }
}

// Выход из системы
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в систему | Алтын Ачкыч</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root { --gold: #D4AF37; --bg: #0F1012; --card: #1E1F23; }
        body { 
            background: var(--bg); 
            color: #fff; 
            font-family: 'Montserrat', sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }
        .login-box {
            background: var(--card);
            padding: 40px;
            border-radius: 20px;
            border: 1px solid rgba(212, 175, 55, 0.3);
            width: 100%;
            max-width: 350px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        h2 { color: var(--gold); margin-bottom: 25px; text-transform: uppercase; letter-spacing: 2px; }
        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            background: #000;
            border: 1px solid #333;
            border-radius: 8px;
            color: #fff;
            box-sizing: border-box;
        }
        input:focus { border-color: var(--gold); outline: none; }
        button {
            width: 100%;
            padding: 15px;
            background: var(--gold);
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover { background: #b8962d; transform: scale(1.02); }
        .error { color: #ff4757; font-size: 0.8rem; margin-bottom: 15px; }
        .back-link { margin-top: 20px; display: block; color: #888; text-decoration: none; font-size: 0.8rem; }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Алтын <span>Ачкыч</span></h2>
    <p style="font-size: 0.8rem; color: #888; margin-bottom: 20px;">Вход в панель управления</p>
    
    <?php if($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Логин" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit" name="login">Войти в систему</button>
    </form>

    <a href="index.html" class="back-link">← Вернуться на сайт</a>
</div>

</body>
</html>
