<?php
// Обработка формы регистрации

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
$formData = [];

// Проверяем, что форма была отправлена методом POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Получаем и валидируем имя
    if (isset($_POST['reg_name'])) {
        $name = validateInput($_POST['reg_name']);
        if (empty($name)) {
            $errors[] = "Имя обязательно для заполнения";
        } elseif (strlen($name) < 2 || strlen($name) > 50) {
            $errors[] = "Имя должно быть от 2 до 50 символов";
        } else {
            $formData['name'] = $name;
        }
    } else {
        $errors[] = "Имя обязательно для заполнения";
    }
    
    // Получаем и валидируем email
    if (isset($_POST['reg_email'])) {
        $email = trim($_POST['reg_email']);
        if (empty($email)) {
            $errors[] = "Email обязателен для заполнения";
        } elseif (!validateEmail($email)) {
            $errors[] = "Некорректный формат email";
        } else {
            $formData['email'] = $email;
        }
    } else {
        $errors[] = "Email обязателен для заполнения";
    }
    
    // Получаем и валидируем пароль
    if (isset($_POST['reg_password'])) {
        $password = $_POST['reg_password'];
        if (empty($password)) {
            $errors[] = "Пароль обязателен для заполнения";
        } elseif (strlen($password) < 6) {
            $errors[] = "Пароль должен быть не менее 6 символов";
        } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errors[] = "Пароль должен содержать буквы и цифры";
        } else {
            $formData['password'] = $password;
        }
    } else {
        $errors[] = "Пароль обязателен для заполнения";
    }
    
    // Получаем и проверяем подтверждение пароля
    if (isset($_POST['reg_password_confirm'])) {
        $passwordConfirm = $_POST['reg_password_confirm'];
        if (empty($passwordConfirm)) {
            $errors[] = "Подтвердите пароль";
        } elseif ($passwordConfirm !== $_POST['reg_password']) {
            $errors[] = "Пароли не совпадают";
        }
    } else {
        $errors[] = "Подтвердите пароль";
    }
    
    // Если нет ошибок, регистрируем пользователя
    if (empty($errors)) {
        $success = true;
        
        // В реальном проекте здесь будет сохранение в базу данных
        // Для демонстрации сохраним в файл
        $userData = [
            'name' => $formData['name'],
            'email' => $formData['email'],
            'password_hash' => password_hash($formData['password'], PASSWORD_DEFAULT),
            'registered_at' => date('Y-m-d H:i:s')
        ];
        
        $logEntry = date('Y-m-d H:i:s') . " | REGISTER | ";
        $logEntry .= json_encode($userData, JSON_UNESCAPED_UNICODE) . "\n";
        file_put_contents('../users.log', $logEntry, FILE_APPEND);
        
        // Создаем сессию пользователя
        $_SESSION['user_logged'] = true;
        $_SESSION['user_email'] = $formData['email'];
        $_SESSION['user_name'] = $formData['name'];
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - DNS</title>
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
            <h2>Регистрация нового пользователя</h2>
            
            <?php if ($success): ?>
                <div style="background: #d4edda; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
                    <h3 style="color: #155724;">✓ Регистрация успешна!</h3>
                    <p>Добро пожаловать, <?php echo htmlspecialchars($formData['name']); ?>!</p>
                    <p>Ваш аккаунт успешно создан.</p>
                    <p>Email: <?php echo htmlspecialchars($formData['email']); ?></p>
                    <p>Теперь вы можете войти в личный кабинет и совершать покупки.</p>
                </div>
                <div style="margin-top: 20px;">
                    <a href="../auth.html" style="background: #ff6600; color: white; padding: 10px 20px; text-decoration: none; border-radius: 3px;">Войти в кабинет</a>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div style="background: #f8d7da; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
                    <h3 style="color: #721c24;">⚠ Ошибки при регистрации:</h3>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div style="margin-top: 30px;">
                <a href="../auth.html">← Вернуться к форме регистрации</a>
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
