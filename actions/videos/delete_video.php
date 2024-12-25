<?php
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // JSONデータを取得
    $data = json_decode(file_get_contents('php://input'), true);

    // JSONデコードのエラーチェック
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['error' => 'JSONデコードエラー']);
        exit;
    }    

    // データの内容を確認（デバッグ用）
    // var_dump($data); // 本番環境では不要

    // IDが指定されていない場合のエラーハンドリング
    $videoId = $data['id'] ?? null;
    if (!$videoId) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => '動画IDが指定されていません。']);
        exit;
    }

    // データベースから削除
    $stmt = $pdo->prepare("DELETE FROM videos WHERE id = :id");
    $stmt->bindParam(':id', $videoId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // 最新の動画リストを取得して返す
        $stmt = $pdo->prepare("SELECT id, title, url, category FROM videos ORDER BY id DESC");
        $stmt->execute();
        $videos = $stmt->fetchAll(PDO::FETCH_ASSOC); // 連想配列として取得

        // 動画リストが存在しない場合のチェック
        if (empty($videos)) {
            echo json_encode(['message' => '動画がありません。']);
        } else {
            echo json_encode($videos); // JSON形式で返す
        }
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'データベースから削除できませんでした。']);
    }
}
?>
