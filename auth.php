<?php
// ========================================
// РЕГИСТРАЦИЯ И АВТОРИЗАЦИЯ ПОЛЬЗОВАТЕЛЕЙ
// Лабораторная работа №2
// ========================================

session_start();

$mode = $_GET['mode'] ?? 'login'; // login или register
$errors = array();
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // РЕГИСТРАЦИЯ
    if (isset($_POST['register'])) {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        $phone = trim($_POST['phone'] ?? '');
        $agree = isset($_POST['agree']);
        
        // Валидация
        if (empty($username)) {
            $errors[] = "Введите логин";
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Введите корректный email";
        }
        if (empty($password)) {
            $errors[] = "Введите пароль";
        } elseif (strlen($password) < 6) {
            $errors[] = "Пароль должен быть не менее 6 символов";
        }
        if ($password !== $password_confirm) {
            $errors[] = "Пароли не совпадают";
        }
        if (!$agree) {
            $errors[] = "Необходимо согласиться с условиями";
        }
        
        if (empty($errors)) {
            // Здесь код для сохранения в БД
            // Для примера просто показываем успех
            $success = "Регистрация успешна! Теперь вы можете войти.";
            $mode = 'login';
        }
    }
    
    // АВТОРИЗАЦИЯ
    if (isset($_POST['login'])) {
        $login_email = trim($_POST['login_email'] ?? '');
        $login_password = $_POST['login_password'] ?? '';
        $remember = isset($_POST['remember']);
        
        if (empty($login_email)) {
            $errors[] = "Введите email или логин";
        }
        if (empty($login_password)) {
            $errors[] = "Введите пароль";
        }
        
        if (empty($errors)) {
            // Здесь проверка данных в БД
            // Для примера просто показываем успех
            $_SESSION['user'] = $login_email;
            $_SESSION['logged_in'] = true;
            $success = "Вы успешно вошли в систему!";
        }
    }
}

// ВЫХОД
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: auth.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $mode == 'login' ? 'Вход' : 'Регистрация'; ?> - DNS</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <h1>🛒 DNS</h1>
        <p>Личный кабинет</p>
    </header>

    <nav>
        <ul>
            <li><a href="index.html">Главная</a></li>
            <li><a href="catalog.html">Каталог</a></li>
            <li><a href="contacts.html">Контакты</a></li>
            <li><a href="auth.php">Личный кабинет</a></li>
        </ul>
    </nav>

    <main>
        <hr>
        
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
            <!-- ПОЛЬЗОВАТЕЛЬ АВТОРИЗОВАН -->
            <div class="success-message">
                <h3>✅ Вы вошли как: <?php echo htmlspecialchars($_SESSION['user']); ?></h3>
                <p>Добро пожаловать в личный кабинет DNS!</p>
                <a href="auth.php?logout=1" class="btn-submit">🚪 Выйти</a>
            </div>
        <?php else: ?>
            <!-- ПОЛЬЗОВАТЕЛЬ НЕ АВТОРИЗОВАН -->
            
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <strong>⚠️ Ошибки:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message">
                    <strong>✅ <?php echo $success; ?></strong>
                </div>
            <?php endif; ?>

            <!-- Переключатель между входом и регистрацией -->
            <div style="text-align: center; margin-bottom: 30px;">
                <a href="auth.php?mode=login" 
                   style="padding: 10px 20px; background: <?php echo $mode == 'login' ? '#ff6600' : '#ddd'; ?>; color: <?php echo $mode == 'login' ? 'white' : '#333'; ?>; text-decoration: none; border-radius: 5px; margin: 0 5px;">
                    🔐 Вход
                </a>
                <a href="auth.php?mode=register" 
                   style="padding: 10px 20px; background: <?php echo $mode == 'register' ? '#ff6600' : '#ddd'; ?>; color: <?php echo $mode == 'register' ? 'white' : '#333'; ?>; text-decoration: none; border-radius: 5px; margin: 0 5px;">
                    📝 Регистрация
                </a>
            </div>

            <?php if ($mode == 'login'): ?>
                <!-- ФОРМА ВХОДА -->
                <h2>Вход в личный кабинет</h2>
                <div class="form-container">
                    <form method="post" action="auth.php?mode=login">
                        <div class="form-group">
                            <label for="login_email">Email или логин:</label>
                            <input type="text" id="login_email" name="login_email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="login_password">Пароль:</label>
                            <input type="password" id="login_password" name="login_password" required>
                        </div>
                        
                        <div class="form-group checkbox-group">
                            <label>
                                <input type="checkbox" name="remember">
                                Запомнить меня
                            </label>
                        </div>
                        
                        <button type="submit" name="login" class="btn-submit">🔑 Войти</button>
                    </form>
                </div>
            <?php else: ?>
                <!-- ФОРМА РЕГИСТРАЦИИ -->
                <h2>Регистрация нового пользователя</h2>
                <div class="form-container">
                    <form method="post" action="auth.php?mode=register">
                        <div class="form-group">
                            <label for="username">Логин: *</label>
                            <input type="text" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email: *</label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Телефон:</label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" 
                                   placeholder="+7 (___) ___-__-__">
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Пароль: *</label>
                            <input type="password" id="password" name="password" required>
                            <small class="text-muted">Минимум 6 символов</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirm">Подтвердите пароль: *</label>
                            <input type="password" id="password_confirm" name="password_confirm" required>
                        </div>
                        
                        <div class="form-group checkbox-group">
                            <label>
                                <input type="checkbox" name="agree" required>
                                Я согласен с <a href="#">условиями использования</a> *
                            </label>
                        </div>
                        
                        <button type="submit" name="register" class="btn-submit">📝 Зарегистрироваться</button>
                    </form>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <footer>
        <hr>
        <p>&copy; 2024 DNS. Все права защищены.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>