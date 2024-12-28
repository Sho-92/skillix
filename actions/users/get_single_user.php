<?php
require_once '../../includes/db.php';

try {
    // ユーザーIDがリクエストに含まれているかチェック
    if (isset($_GET['id'])) {
        $userId = $_GET['id'];

        // 特定のユーザーを取得するSQL
        $stmt = $pdo->prepare("SELECT id, username, employee_id, password, role FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($user, JSON_UNESCAPED_UNICODE);
        } else {
            // ユーザーが見つからない場合
            http_response_code(404); // Not Found
            echo json_encode(['error' => 'ユーザーが見つかりません。']);
        }
    } else {
        // ユーザーIDが渡されていない場合
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'ユーザーIDが指定されていません。']);
    }
} catch (Exception $e) {
    // エラーハンドリング
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'エラー: ' . $e->getMessage()]);
}
?>
