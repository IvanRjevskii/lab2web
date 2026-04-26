<?php
// Обработка формы авторизации

session_start();

// Функция для валидации и очистки данных
function validateInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Функция для валидации email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

$errors = [];
$success = false;

// Проверяем, что форма была отправлена методом POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Получаем и валидируем email
    if (isset($_POST['login_email'])) {
        $email = trim($_POST['login_email']);
        if (empty($email)) {
            $errors[] = "Email обязателен для заполнения";
        } elseif (!validateEmail($email)) {
            $errors[] = "Некорректный формат email";
        } else {
            $email = validateInput($email);
        }
    } else {
        $errors[] = "Email обязателен для заполнения";
    }
    
    // Получаем и валидируем пароль
    if (isset($_POST['login_password'])) {
        $password = $_POST['login_password'];
        if (empty($password)) {
            $errors[] = "Пароль обязателен для заполнения";
        } elseif (strlen($password) < 6) {
            $errors[] = "Пароль должен быть не менее 6 символов";
        }
    } else {
        $errors[] = "Пароль обязателен для заполнения";
    }
    
    // Если нет ошибок, проверяем данные (в реальном проекте - проверка по БД)
    if (empty($errors)) {
        // Для демонстрации - простая проверка
        // В реальном проекте здесь будет запрос к базе данных
        $success = true;
        
        // Создаем сессию пользователя
        $_SESSION['user_logged'] = true;
        $_SESSION['user_email'] = $email;
        $_SESSION['login_time'] = date('Y-m-d H:i:s');
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - DNS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>🛒 DNS</h1>
            <nav>
                <ul>
                    <li><a href="../index.html">Главная</a></li>
                    <li><a href="../catalog.html">Каталог</a></li>
                    <li><a href="../contacts.html">Контакты</a></li>
                    <li><a href="../guestbook.html">Гостевая книга</a></li>
                    <li><a href="../auth.html">Вход/Регистрация</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="search-results">
            <h2>Вход в личный кабинет</h2>
            
            <?php if ($success): ?>
                <div style="background: #d4edda; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
                    <h3 style="color: #155724;">✓ Вы успешно вошли!</h3>
                    <p>Добро пожаловать, <?php echo htmlspecialchars($email); ?>!</p>
                    <p>Время входа: <?php echo $_SESSION['login_time']; ?></p>
                    <p>Теперь вы можете совершать покупки и оставлять отзывы.</p>
                </div>
                <div style="margin-top: 20px;">
                    <a href="../index.html" style="background: #ff6600; color: white; padding: 10px 20px; text-decoration: none; border-radius: 3px;">Перейти на главную</a>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div style="background: #f8d7da; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
                    <h3 style="color: #721c24;">⚠ Ошибки при входе:</h3>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div style="margin-top: 30px;">
                <a href="../auth.html">← Вернуться к форме входа</a>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>© 2024 DNS. Все права защищены.</p>
        </div>
    </footer>
</body>
</html>
