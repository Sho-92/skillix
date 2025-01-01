<?php
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // JSONデータを受け取る
    $data = json_decode(file_get_contents('php://input'), true);
    error_log("Received Data: " . print_r($data, true));  // 受け取ったデータをログに出力

    $title = $data['title'] ?? '';
    $url = $data['url'] ?? '';
    $categoryId = $data['category_id'] ?? ''; // ここでcategory_idを受け取る

    error_log("title: $title, url: $url, category_id: $categoryId"); // 受け取ったcategory_idをログに出力

    // 必須チェック
    if (empty($title) || empty($url)) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'タイトルとURLは必須です。']);
        exit;
    }

    // URLを埋め込み形式に変換
    $videoId = preg_replace('/.*(?:\/|v=)([a-zA-Z0-9_-]+).*$/', '$1', $url);
    $embedUrl = "https://www.youtube.com/embed/$videoId";

    try {
        // category_idが空の場合、エラーメッセージを表示
        if (empty($categoryId)) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'カテゴリーIDが指定されていません。']);
            exit;
        }

        // 新しく挿入する動画のpositionを決定する
        // まず、categoryId内での最大のpositionを取得
        $stmt = $pdo->prepare("SELECT MAX(position) AS max_position FROM videos WHERE category_id = :category_id");
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        $maxPosition = $stmt->fetch(PDO::FETCH_ASSOC)['max_position'] ?? 0;  // 最大のpositionを取得、ない場合は0を使用

        // 新しい動画のpositionは最大position + 1
        $newPosition = $maxPosition + 1;

        // 動画をvideosテーブルに挿入（positionも含む）
        $stmt = $pdo->prepare("INSERT INTO videos (title, url, category_id, position) VALUES (:title, :url, :category_id, :position)");
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':url', $embedUrl, PDO::PARAM_STR);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(':position', $newPosition, PDO::PARAM_INT);

        error_log("動画登録データ: タイトル: $title, URL: $embedUrl, カテゴリーID: $categoryId, position: $newPosition");

        if ($stmt->execute()) {
            // 最新の動画リストを取得して返す
            $stmt = $pdo->prepare("
                SELECT v.id, v.title, v.url, c.name AS category 
                FROM videos v 
                JOIN categories c ON v.category_id = c.id 
                ORDER BY v.position ASC, v.id DESC
            ");
            $stmt->execute();
            $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($videos); // JSON形式で返す
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => '動画データをデータベースに保存できませんでした。']);
        }

    } catch (Exception $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'エラー: ' . $e->getMessage()]);
    }
}
?>
