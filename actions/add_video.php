<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // JSONデータを受け取る
    $data = json_decode(file_get_contents('php://input'), true);
    $title = $data['title'] ?? '';
    $url = $data['url'] ?? '';

    // 必須チェック
    if (empty($title) || empty($url)) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'タイトルとURLは必須です。']);
        exit;
    }

    // URLを埋め込み形式に変換
    $videoId = preg_replace('/.*(?:\/|v=)([a-zA-Z0-9_-]+).*$/', '$1', $url);
    $embedUrl = "https://www.youtube.com/embed/$videoId";

    // データベースに挿入
    $stmt = $pdo->prepare("INSERT INTO videos (title, url) VALUES (:title, :url)");
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':url', $embedUrl, PDO::PARAM_STR);

    if ($stmt->execute()) {
        // 最新の動画リストを取得して返す
        $stmt = $pdo->prepare("SELECT id, title, url FROM videos ORDER BY id DESC");
        $stmt->execute();
        $videos = $stmt->fetchAll();
        echo json_encode($videos); // JSON形式で返す
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'データベースに保存できませんでした。']);
    }
}
?>
