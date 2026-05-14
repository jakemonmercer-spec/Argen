<?php
/**
 * FULL CRUD API - Агентство "Алтын Ачкыч"
 * Поддержка: GET (чтение), POST (создание), DELETE (удаление)
 */

session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$projectId = "argen-59e33";
$collection = "realty";
$baseUrl = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/{$collection}";

$method = $_SERVER['REQUEST_METHOD'];

// --- 1. ПОЛУЧЕНИЕ ОБЪЕКТОВ (GET) ---
if ($method === 'GET') {
    $ch = curl_init($baseUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    $output = [];

    if (isset($data['documents'])) {
        foreach ($data['documents'] as $doc) {
            $f = $doc['fields'];
            $output[] = [
                'id'      => basename($doc['name']), // ID документа в Firebase
                'title'   => $f['title']['stringValue'] ?? '',
                'price'   => $f['price']['integerValue'] ?? $f['price']['stringValue'] ?? '0',
                'address' => $f['address']['stringValue'] ?? '',
                'img'     => $f['img']['stringValue'] ?? '',
                'type'    => $f['type']['stringValue'] ?? ''
            ];
        }
    }
    echo json_encode($output);
}

// --- 2. ДОБАВЛЕНИЕ ОБЪЕКТА (POST) ---
if ($method === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);

    $postData = [
        "fields" => [
            "title"   => ["stringValue" => $input['title']],
            "price"   => ["integerValue" => (int)$input['price']],
            "address" => ["stringValue" => $input['address']],
            "img"     => ["stringValue" => $input['img']],
            "type"    => ["stringValue" => $input['type']]
        ]
    ];

    $ch = curl_init($baseUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $result = curl_exec($ch);
    curl_close($ch);
    echo $result;
}

// --- 3. УДАЛЕНИЕ ОБЪЕКТА (DELETE) ---
if ($method === 'DELETE') {
    // Проверяем, авторизован ли пользователь через login.php
    if (!isset($_SESSION['auth'])) {
        echo json_encode(["status" => "error", "message" => "Отказ в доступе. Войдите в систему."]);
        exit;
    }

    // Получаем ID документа из параметров запроса (?id=...)
    $docId = $_GET['id'];

    if ($docId) {
        $deleteUrl = $baseUrl . "/" . $docId;
        
        $ch = curl_init($deleteUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            echo json_encode(["status" => "success", "message" => "Объект удален"]);
        } else {
            echo json_encode(["status" => "error", "code" => $httpCode]);
        }
    }
}
?>
