<?php
// ========================================
// ГОСТЕВАЯ КНИГА
// Все элементы управления форм
// Лабораторная работа №2
// ========================================

$message_sent = false;
$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Валидация полей
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rating = $_POST['rating'] ?? '';
    $visit_type = $_POST['visit_type'] ?? array();
    $message = trim($_POST['message'] ?? '');
    $subscribe = isset($_POST['subscribe']) ? true : false;
    $gender = $_POST['gender'] ?? '';
    $city = $_POST['city'] ?? '';
    
    // Проверка обязательных полей
    if (empty($name)) {
        $errors[] = "Введите ваше имя";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Введите корректный email";
    }
    if (empty($rating)) {
        $errors[] = "Выберите оценку";
    }
    if (empty($message)) {
        $errors[] = "Введите сообщение";
    }
    
    // Если ошибок нет, сохраняем
    if (empty($errors)) {
        $message_sent = true;
        // Здесь можно добавить код для сохранения в файл или БД
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Гостевая книга - DNS</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <h1>🛒 DNS</h1>
        <p>Гостевая книга - Оставьте ваш отзыв</p>
    </header>

    <nav>
        <ul>
            <li><a href="index.html">Главная</a></li>
            <li><a href="catalog.html">Каталог</a></li>
            <li><a href="contacts.html">Контакты</a></li>
            <li><a href="guestbook.php">Гостевая книга</a></li>
        </ul>
    </nav>

    <main>
        <hr>
        <h2>Отзыв о нашем магазине</h2>
        
        <?php if ($message_sent): ?>
            <div class="success-message">
                <strong>✅ Спасибо за ваш отзыв!</strong><br>
                Ваше сообщение успешно отправлено.
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <strong>⚠️ Исправьте ошибки:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form method="post" action="guestbook.php">
                
                <!-- Однострочное текстовое поле -->
                <div class="form-group">
                    <label for="name">Ваше имя: *</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="<?php echo htmlspecialchars($name ?? ''); ?>" 
                           placeholder="Иван Иванов"
                           required>
                </div>

                <!-- Email поле -->
                <div class="form-group">
                    <label for="email">Email: *</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?php echo htmlspecialchars($email ?? ''); ?>" 
                           placeholder="example@mail.ru"
                           required>
                </div>

                <!-- Радио кнопки (оценка) -->
                <div class="form-group">
                    <label>Оценка магазина: *</label>
                    <div class="radio-group">
                        <label><input type="radio" name="rating" value="5" <?php echo ($rating ?? '') == '5' ? 'checked' : ''; ?>> ⭐⭐⭐⭐⭐ Отлично</label>
                        <label><input type="radio" name="rating" value="4" <?php echo ($rating ?? '') == '4' ? 'checked' : ''; ?>> ⭐⭐⭐⭐ Хорошо</label>
                        <label><input type="radio" name="rating" value="3" <?php echo ($rating ?? '') == '3' ? 'checked' : ''; ?>> ⭐⭐⭐ Нормально</label>
                        <label><input type="radio" name="rating" value="2" <?php echo ($rating ?? '') == '2' ? 'checked' : ''; ?>> ⭐⭐ Плохо</label>
                        <label><input type="radio" name="rating" value="1" <?php echo ($rating ?? '') == '1' ? 'checked' : ''; ?>> ⭐ Ужасно</label>
                    </div>
                </div>

                <!-- Чекбоксы (цель визита) -->
                <div class="form-group">
                    <label>Цель визита (можно выбрать несколько):</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="visit_type[]" value="purchase" <?php echo in_array('purchase', $visit_type ?? array()) ? 'checked' : ''; ?>> Покупка техники</label>
                        <label><input type="checkbox" name="visit_type[]" value="consultation" <?php echo in_array('consultation', $visit_type ?? array()) ? 'checked' : ''; ?>> Консультация</label>
                        <label><input type="checkbox" name="visit_type[]" value="warranty" <?php echo in_array('warranty', $visit_type ?? array()) ? 'checked' : ''; ?>> Гарантийное обслуживание</label>
                        <label><input type="checkbox" name="visit_type[]" value="exchange" <?php echo in_array('exchange', $visit_type ?? array()) ? 'checked' : ''; ?>> Обмен/возврат</label>
                    </div>
                </div>

                <!-- Выпадающий список (город) -->
                <div class="form-group">
                    <label for="city">Ваш город:</label>
                    <select id="city" name="city">
                        <option value="">-- Выберите город --</option>
                        <option value="moscow" <?php echo ($city ?? '') == 'moscow' ? 'selected' : ''; ?>>Москва</option>
                        <option value="spb" <?php echo ($city ?? '') == 'spb' ? 'selected' : ''; ?>>Санкт-Петербург</option>
                        <option value="vladivostok" <?php echo ($city ?? '') == 'vladivostok' ? 'selected' : ''; ?>>Владивосток</option>
                        <option value="ekb" <?php echo ($city ?? '') == 'ekb' ? 'selected' : ''; ?>>Екатеринбург</option>
                        <option value="other" <?php echo ($city ?? '') == 'other' ? 'selected' : ''; ?>>Другой</option>
                    </select>
                </div>

                <!-- Радио кнопки (пол) -->
                <div class="form-group">
                    <label>Пол:</label>
                    <div class="radio-group">
                        <label><input type="radio" name="gender" value="male" <?php echo ($gender ?? '') == 'male' ? 'checked' : ''; ?>> Мужской</label>
                        <label><input type="radio" name="gender" value="female" <?php echo ($gender ?? '') == 'female' ? 'checked' : ''; ?>> Женский</label>
                        <label><input type="radio" name="gender" value="other" <?php echo ($gender ?? '') == 'other' ? 'checked' : ''; ?>> Другой</label>
                    </div>
                </div>

                <!-- Многострочное текстовое поле -->
                <div class="form-group">
                    <label for="message">Ваш отзыв: *</label>
                    <textarea id="message" 
                              name="message" 
                              rows="6" 
                              placeholder="Расскажите о вашем опыте посещения нашего магазина..."
                              required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                </div>

                <!-- Чекбокс (подписка) -->
                <div class="form-group checkbox-group">
                    <label>
                        <input type="checkbox" name="subscribe" <?php echo ($subscribe ?? false) ? 'checked' : ''; ?>>
                        Подписаться на новости и акции DNS
                    </label>
                </div>

                <!-- Кнопка отправки -->
                <button type="submit" class="btn-submit">📤 Отправить отзыв</button>
            </form>
        </div>
    </main>

    <footer>
        <hr>
        <p>&copy; 2024 DNS. Все права защищены.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>