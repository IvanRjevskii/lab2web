<?php
// ========================================
// ПОИСК ТОВАРОВ ПО ПЕРВОЙ БУКВЕ
// Работа с базой данных MySQL
// Лабораторная работа №2, Задание 2
// ========================================

$search_q = "";
$results = array();
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Валидация и очистка input
    if (isset($_POST['search_q'])) {
        $search_q = trim($_POST['search_q']);
        $search_q = htmlspecialchars($search_q);
        $search_q = mb_strtolower($search_q, 'UTF-8');
        
        // Проверка на пустоту
        if (empty($search_q)) {
            $error = "Введите букву для поиска";
        } elseif (mb_strlen($search_q, 'UTF-8') != 1) {
            $error = "Введите только одну букву";
        } elseif (!preg_match('/^[а-яa-z]$/ui', $search_q)) {
            $error = "Введите корректную букву (кириллица или латиница)";
        } else {
            // Подключение к базе данных
            $l = mysqli_connect('localhost', 'root', '', 'dns_site');
            
            // Проверка подключения
            if (!$l) {
                die("Ошибка подключения: " . mysqli_connect_error());
            }
            
            // Установка кодировки
            mysqli_set_charset($l, "utf8");
            
            // Первая буква для поиска
            $first_letter = mb_substr($search_q, 0, 1, 'UTF-8');
            
            // SQL запрос - поиск по первой букве
            $query = "SELECT * FROM products WHERE LOWER(LEFT(name, 1)) = ? ORDER BY name";
            
            // Подготовка запроса
            $stmt = mysqli_prepare($l, $query);
            mysqli_stmt_bind_param($stmt, "s", $first_letter);
            mysqli_stmt_execute($stmt);
            
            // Получение результатов
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $results[] = $row;
                }
            } else {
                $error = "Товары на букву '" . mb_strtoupper($search_q, 'UTF-8') . "' не найдены";
            }
            
            // Закрытие соединения
            mysqli_stmt_close($stmt);
            mysqli_close($l);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск товаров - DNS</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <h1>🛒 DNS</h1>
        <p>Поиск товаров</p>
    </header>

    <nav>
        <ul>
            <li><a href="index.html">Главная</a></li>
            <li><a href="catalog.html">Каталог</a></li>
            <li><a href="contacts.html">Контакты</a></li>
            <li><a href="search.php">Поиск</a></li>
        </ul>
    </nav>

    <main>
        <hr>
        <h2>Поиск товаров по первой букве</h2>
        
        <div class="form-container">
            <form name="f1" method="post" action="search.php">
                <div class="form-group">
                    <label for="search_q">Введите первую букву названия товара:</label>
                    <input type="search" 
                           id="search_q" 
                           name="search_q" 
                           value="<?php echo htmlspecialchars($search_q); ?>" 
                           placeholder="Например: С" 
                           maxlength="1"
                           required>
                    <small class="text-muted">Введите одну букву (кириллица или латиница)</small>
                </div>
                <button type="submit" class="btn-submit">🔍 Поиск</button>
            </form>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <strong>⚠️ <?php echo $error; ?></strong>
            </div>
        <?php endif; ?>

        <?php if (!empty($results)): ?>
            <h3>Найдено товаров: <?php echo count($results); ?></h3>
            <div class="catalog-grid">
                <?php foreach ($results as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                 width="200">
                        </div>
                        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                        <p><strong>Категория:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
                        <p><strong>Цена:</strong> <span style="color: #ff6600; font-size: 18px;"><?php echo htmlspecialchars($product['price']); ?></span></p>
                        <p class="short-description"><?php echo htmlspecialchars($product['description']); ?></p>
                        <?php if ($product['page_url'] != '#'): ?>
                            <a href="<?php echo htmlspecialchars($product['page_url']); ?>" class="btn-submit">Подробнее</a>
                        <?php else: ?>
                            <span style="color: #999;">Страница в разработке</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <hr>
        <h3>📋 Доступные товары в каталоге:</h3>
        <ul class="characteristics-list">
            <li><strong>С</strong> - Смартфон XYZ Pro (29990 ₽)</li>
            <li><strong>Н</strong> - Ноутбук UltraBook 15 (54990 ₽)</li>
            <li><strong>Н</strong> - Наушники Wireless Pro (8990 ₽)</li>
            <li><strong>П</strong> - Планшет Tab S (34990 ₽)</li>
            <li><strong>У</strong> - Умные часы Smart Watch (12990 ₽)</li>
        </ul>
    </main>

    <footer>
        <hr>
        <p>&copy; 2024 DNS. Все права защищены.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>