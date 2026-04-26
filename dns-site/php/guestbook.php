<?php
// Обработка формы гостевой книги (отзывов)

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
    if (isset($_POST['name'])) {
        $name = validateInput($_POST['name']);
        if (empty($name)) {
            $errors[] = "Имя обязательно для заполнения";
        } elseif (strlen($name) < 2 || strlen($name) > 50) {
            $errors[] = "Имя должно быть от 2 до 50 символов";
        } else {
            $formData['name'] = $name;
        }
    }
    
    // Получаем и валидируем email
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        if (empty($email)) {
            $errors[] = "Email обязателен для заполнения";
        } elseif (!validateEmail($email)) {
            $errors[] = "Некорректный формат email";
        } else {
            $formData['email'] = $email;
        }
    }
    
    // Получаем и валидируем тему отзыва
    if (isset($_POST['subject'])) {
        $subject = validateInput($_POST['subject']);
        if (empty($subject)) {
            $errors[] = "Тема отзыва обязательна";
        } elseif (strlen($subject) < 3 || strlen($subject) > 100) {
            $errors[] = "Тема должна быть от 3 до 100 символов";
        } else {
            $formData['subject'] = $subject;
        }
    }
    
    // Получаем и валидируем сообщение
    if (isset($_POST['message'])) {
        $message = validateInput($_POST['message']);
        if (empty($message)) {
            $errors[] = "Сообщение обязательно для заполнения";
        } elseif (strlen($message) < 10 || strlen($message) > 1000) {
            $errors[] = "Сообщение должно быть от 10 до 1000 символов";
        } else {
            $formData['message'] = $message;
        }
    }
    
    // Получаем оценку (rating)
    if (isset($_POST['rating'])) {
        $rating = intval($_POST['rating']);
        if ($rating >= 1 && $rating <= 5) {
            $formData['rating'] = $rating;
        } else {
            $errors[] = "Оценка должна быть от 1 до 5";
        }
    }
    
    // Получаем рекомендацию
    if (isset($_POST['recommend'])) {
        $recommend = validateInput($_POST['recommend']);
        if (in_array($recommend, ['yes', 'no', 'maybe'])) {
            $formData['recommend'] = $recommend;
        }
    }
    
    // Получаем категории (массив)
    if (isset($_POST['categories']) && is_array($_POST['categories'])) {
        $categories = [];
        foreach ($_POST['categories'] as $category) {
            $cat = validateInput($category);
            if (in_array($cat, ['smartphones', 'laptops', 'audio', 'tv', 'appliances'])) {
                $categories[] = $cat;
            }
        }
        $formData['categories'] = $categories;
    }
    
    // Получаем город
    if (isset($_POST['city'])) {
        $city = validateInput($_POST['city']);
        $formData['city'] = $city;
    }
    
    // Получаем источник
    if (isset($_POST['source'])) {
        $source = validateInput($_POST['source']);
        $formData['source'] = $source;
    }
    
    // Получаем пожелания
    if (isset($_POST['wishes'])) {
        $wishes = validateInput($_POST['wishes']);
        $formData['wishes'] = $wishes;
    }
    
    // Если нет ошибок, сохраняем данные
    if (empty($errors)) {
        $success = true;
        
        // Здесь можно добавить код для сохранения данных в файл или базу данных
        // Для примера сохраним в текстовый файл
        $logEntry = date('Y-m-d H:i:s') . " | ";
        $logEntry .= json_encode($formData, JSON_UNESCAPED_UNICODE) . "\n";
        file_put_contents('../reviews.log', $logEntry, FILE_APPEND);
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Обработка отзыва - DNS</title>
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
            <h2>Отправка отзыва</h2>
            
            <?php if ($success): ?>
                <div style="background: #d4edda; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
                    <h3 style="color: #155724;">✓ Спасибо за ваш отзыв!</h3>
                    <p>Ваш отзыв успешно отправлен. Мы обязательно рассмотрим его в ближайшее время.</p>
                    <p><strong>Ваше имя:</strong> <?php echo htmlspecialchars($formData['name']); ?></p>
                    <p><strong>Оценка:</strong> <?php echo str_repeat('⭐', $formData['rating']); ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div style="background: #f8d7da; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
                    <h3 style="color: #721c24;">⚠ Ошибки при отправке:</h3>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div style="margin-top: 30px;">
                <a href="../guestbook.html">← Вернуться к форме отзыва</a>
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
