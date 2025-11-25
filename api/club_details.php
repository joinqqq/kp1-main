<?php
// api/club_details.php
header('Content-Type: application/json');
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Получаем ID клуба из параметра GET
$club_id = $_GET['id'] ?? null;

if (!$club_id || !is_numeric($club_id)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid club ID"]);
    exit();
}

try {
    // Запрос для получения информации о клубе (БЕЗ image_url)
    $query = "
        SELECT 
            id, name, description, rating, address, city, hourly_rate, 
            open_time, close_time, is_24h
        FROM clubs 
        WHERE id = ? 
        LIMIT 1
    ";
    $stmt = $db->prepare($query);
    $stmt->execute([$club_id]);

    $club = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$club) {
        http_response_code(404);
        echo json_encode(["error" => "Club not found"]);
        exit();
    }

    // --- Добавляем характеристики ПК ---
    $computers_query = "
        SELECT DISTINCT cpu, gpu, ram, monitor, zone
        FROM computers
        WHERE club_id = ?
        AND is_active = TRUE
    ";
    $computers_stmt = $db->prepare($computers_query);
    $computers_stmt->execute([$club_id]);
    $pc_specs = $computers_stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- Добавляем фото из таблицы club_photos ---
    $photos_query = "SELECT photo_url FROM club_photos WHERE club_id = ? ORDER BY id ASC";
    $photos_stmt = $db->prepare($photos_query);
    $photos_stmt->execute([$club_id]);
    $photos = $photos_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Если фото нет, добавим заглушку или оставим пустым массивом
    if (empty($photos)) {
        // Можно добавить заглушку, если хочешь
        // $photos = [['photo_url' => 'images/no-photo.jpg']];
    }

    // --- Добавляем отзывы ---
    $reviews_query = "
        SELECT r.rating, r.comment, r.created_at, u.first_name, u.last_name
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        WHERE r.club_id = ?
        ORDER BY r.created_at DESC
        LIMIT 5
    ";
    $reviews_stmt = $db->prepare($reviews_query);
    $reviews_stmt->execute([$club_id]);
    $reviews = $reviews_stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- Создаем объект ответа ---
    $club_details = [
        "id" => $club['id'],
        "name" => $club['name'],
        "description" => $club['description'] ?? "Информация о клубе скоро появится.",
        "rating" => $club['rating'],
        "address" => $club['address'],
        "city" => $club['city'],
        "hourly_rate" => $club['hourly_rate'],
        "is_24h" => $club['is_24h'],
        "open_time" => $club['open_time'],
        "close_time" => $club['close_time'],
        "pc_specs" => $pc_specs,
        "photos" => $photos, // Теперь это фото из club_photos
        "reviews" => $reviews
    ];

    // Отправляем JSON
    echo json_encode($club_details, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    // В случае любой ошибки, отправляем JSON с ошибкой
    http_response_code(500);
    echo json_encode([
        "error" => "Internal Server Error",
        "message" => $e->getMessage(),
        // "trace" => $e->getTraceAsString() // Можно раскомментировать для отладки
    ]);
}
?>