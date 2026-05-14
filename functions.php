<?php
/**
 * CORE LOGIC - Агентство недвижимости "Алтын Ачкыч"
 * Этот файл содержит серверные функции для работы с базой данных Firebase
 */

// Конфигурация проекта
define('FIREBASE_URL', 'https://firestore.googleapis.com/v1/projects/argen-59e33/databases/(default)/documents/realty');

/**
 * Функция для получения всех объектов из базы через PHP cURL
 */
function get_all_realty() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, FIREBASE_URL . "?orderBy=price desc"); // Сортировка через API
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Для работы на локальных серверах
    
    $response = curl_exec($ch);
    $data = json_decode($response, true);
    curl_close($ch);

    $results = [];
    if (isset($data['documents'])) {
        foreach ($data['documents'] as $doc) {
            $fields = $doc['fields'];
            // Логика форматирования данных
            $results[] = [
                'id'      => basename($doc['name']),
                'title'   => $f['title']['stringValue'] ?? 'Без названия',
                'price'   => isset($fields['price']['integerValue']) ? $fields['price']['integerValue'] : (isset($fields['price']['stringValue']) ? $fields['price']['stringValue'] : 0),
                'address' => $fields['address']['stringValue'] ?? 'Адрес не указан',
                'img'     => $fields['img']['stringValue'] ?? 'https://via.placeholder.com/400x300?text=No+Photo',
                'type'    => $fields['type']['stringValue'] ?? 'Объект'
            ];
        }
    }
    return $results;
}

/**
 * Функция форматирования цены (например: 50000 -> 50 000 $)
 */
function format_price($price) {
    return number_format((float)$price, 0, '.', ' ') . ' $';
}

/**
 * Логика фильтрации поиска на стороне сервера
 */
function search_filter($data, $query) {
    if (empty($query)) return $data;
    
    $query = mb_strtolower($query, 'UTF-8');
    return array_filter($data, function($item) use ($query) {
        return mb_strpos(mb_strtolower($item['title'], 'UTF-8'), $query) !== false || 
               mb_strpos(mb_strtolower($item['address'], 'UTF-8'), $query) !== false;
    });
}

// Конец файла логики
