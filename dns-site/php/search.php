<?php
// Поиск товаров по первой букве названия
// Задание 2: поиск товаров с использованием ассоциативного массива

// Многомерный ассоциативный массив с товарами
$products = [
    'smartphone' => [
        'name' => 'Смартфон XYZ Pro',
        'description' => 'Современный смартфон с мощным процессором и камерой 108 Мп.',
        'price' => 29990,
        'specs' => [
            'Экран' => '6.7" AMOLED, 120 Гц',
            'Процессор' => '8-ядерный, 2.8 ГГц',
            'Память' => '8 ГБ ОЗУ / 256 ГБ ПЗУ',
            'Камера' => '108 Мп + 12 Мп + 5 Мп',
            'Аккумулятор' => '5000 мА·ч'
        ],
        'url' => 'products/smartphone.html'
    ],
    'laptop' => [
        'name' => 'Ноутбук UltraBook 15"',
        'description' => 'Производительный ноутбук для работы, учебы и развлечений.',
        'price' => 54990,
        'specs' => [
            'Экран' => '15.6" IPS, 1920x1080',
            'Процессор' => 'Intel Core i5-1135G7',
            'ОЗУ' => '8 ГБ DDR4',
            'Накопитель' => '512 ГБ SSD',
            'Видеокарта' => 'Intel Iris Xe Graphics'
        ],
        'url' => 'products/laptop.html'
    ],
    'headphones' => [
        'name' => 'Наушники Wireless Pro',
        'description' => 'Беспроводные наушники с активным шумоподавлением.',
        'price' => 7990,
        'specs' => [
            'Тип' => 'Беспроводные, накладные',
            'Время работы' => 'До 30 часов',
            'Зарядка' => 'USB Type-C',
            'Вес' => '250 г'
        ],
        'url' => 'products/headphones.html'
    ]
];

// Получаем поисковый запрос от пользователя
$search_q = isset($_POST['search_q']) ? $_POST['search_q'] : '';

// Валидация и очистка входных данных
function validateSearchQuery($query) {
    // Удаляем лишние пробелы
    $query = trim($query);
    // Удаляем HTML теги для безопасности
    $query = strip_tags($query);
    // Удаляем специальные символы, оставляем только буквы (включая кириллицу) и цифры
    $query = preg_replace('/[^\p{L}\p{N}]/u', '', $query);
    // Ограничиваем длину запроса
    if (strlen($query) > 50) {
        $query = substr($query, 0, 50);
    }
    return $query;
}

// Очищаем поисковый запрос
$search_q = validateSearchQuery($search_q);

// Функция поиска товаров по первой букве
function searchProducts($query, $products) {
    $results = [];
    
    // Если запрос пустой, возвращаем все товары
    if (empty($query)) {
        return $products;
    }
    
    // Получаем первую букву запроса (приводим к нижнему регистру)
    $firstLetter = mb_strtolower(mb_substr($query, 0, 1));
    
    // Ищем товары, название которых начинается с этой буквы
    foreach ($products as $key => $product) {
        $productNameFirstLetter = mb_strtolower(mb_substr($product['name'], 0, 1));
        
        // Сравниваем первые буквы
        if ($productNameFirstLetter === $firstLetter) {
            $results[$key] = $product;
        }
    }
    
    return $results;
}

// Выполняем поиск
$searchResults = searchProducts($search_q, $products);

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результаты поиска - DNS</title>
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
            <h2>Результаты поиска</h2>
            
            <?php if (!empty($search_q)): ?>
                <p>Поисковый запрос: "<strong><?php echo htmlspecialchars($search_q); ?></strong>"</p>
            <?php endif; ?>
            
            <?php if (count($searchResults) > 0): ?>
                <p>Найдено товаров: <?php echo count($searchResults); ?></p>
                
                <?php foreach ($searchResults as $key => $product): ?>
                    <div class="search-result-item">
                        <h3><a href="<?php echo htmlspecialchars($product['url']); ?>">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </a></h3>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                        <p><strong>Цена:</strong> <?php echo number_format($product['price'], 0, '.', ' '); ?> ₽</p>
                        
                        <h4>Характеристики:</h4>
                        <ul>
                            <?php foreach ($product['specs'] as $specName => $specValue): ?>
                                <li><strong><?php echo htmlspecialchars($specName); ?>:</strong> <?php echo htmlspecialchars($specValue); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
                
            <?php else: ?>
                <div class="no-results">
                    <p>😔 По вашему запросу ничего не найдено.</p>
                    <p>Попробуйте ввести другую букву или вернитесь на <a href="../index.html">главную страницу</a>.</p>
                </div>
            <?php endif; ?>
            
            <div style="margin-top: 30px;">
                <a href="../index.html" class="back-link">← Вернуться к поиску</a>
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
