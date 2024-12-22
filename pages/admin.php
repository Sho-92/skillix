<?php
session_start(); // セッションを開始

require_once '../includes/functions.php'; // functions.php をインクルードして、checkAccess関数を使用できるようにする
// 本社ユーザー専用ページを確認
checkAccess('head_office'); // 本社のみアクセス許可

$page = 'admin'; // ページ名設定

require_once '../includes/db.php'; // DB接続ファイルを読み込み
// 動画追加処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'] ?? '';
  $url = $_POST['url'] ?? '';

  if (!empty($title) && !empty($url)) {
    $stmt = $pdo->prepare("INSERT INTO videos (title, url, uploaded_at) VALUES (:title, :url, NOW())");
    $stmt->execute([':title' => $title, ':url' => $url]);
    echo "動画が正常に追加されました。";
  } else {
    echo "全てのフィールドを入力してください。";
  }
  exit;
}
// 登録済み動画一覧を取得
$stmt = $pdo->query("SELECT id, title, url FROM videos ORDER BY uploaded_at DESC");
$videos = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<main class="main">
  <a href="dashboard.php">ダッシュボード</a>
  <h2>本社用：動画リンク入力セクション</h2>
  <section class="input-video">
    <input type="text" id="videoTitle" class="video-title" placeholder="タイトルを入力">
    <input type="text" id="videoUrl" class="video-url" placeholder="YouTube URL">
    <button onclick="addVideo()">追加</button>
  </section>
  <h2>登録済み動画一覧</h2>
  <section class="admin-video-list">
    <ul id="adminVideoList">
      <!-- JavaScriptでリストを動的に追加 -->
    </ul>
  </section>
</main>

<?php include '../includes/footer.php'; ?>