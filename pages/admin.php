<?php
session_start(); // セッションを開始

require_once '../includes/functions.php'; // functions.php をインクルードして、checkAccess関数を使用できるようにする
// 本社ユーザー専用ページを確認
checkAccess('admin'); // 本社のみアクセス許可

$page = 'admin'; // ページ名設定

require_once '../includes/db.php'; // DB接続ファイルを読み込み
// 動画追加処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'] ?? '';
  $url = $_POST['url'] ?? '';

  if (!empty($title) && !empty($url)) {
    $stmt = $pdo->prepare("INSERT INTO videos (title, url, created_at) VALUES (:title, :url, NOW())");
    $stmt->execute([':title' => $title, ':url' => $url]);
    echo "動画が正常に追加されました。";
  } else {
    echo "全てのフィールドを入力してください。";
  }
  exit;
}
// 登録済み動画一覧を取得
$stmt = $pdo->query("SELECT id, title, url FROM videos ORDER BY created_at DESC");
$videos = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<main class="main">
  <h2>本社用：動画リンク管理ツール</h2>
  <div class="input-content">
    <section class="input-video">
      <input type="text" id="videoTitle" class="video-title" placeholder="タイトルを入力">
      <input type="text" id="videoUrl" class="video-url" placeholder="YouTube URL">
      <div class="category-input">
        <label for="categorySelect">カテゴリーを選択または追加:</label>
        <select id="categorySelect" class="category-select">
          <option value="">-- カテゴリーを選択 --</option>
          <!-- 既存のカテゴリーがここに動的に追加される -->
        </select>
        <input type="text" id="newCategory" class="new-category" placeholder="新しいカテゴリーを追加">
      </div>
      <button onclick="addVideo()">追加</button>
    </section>
  </div>

  <div class="main-content">
    <div class="side-content">
      <h2 class="dashboard-title">メインメニュー</h2>
      <div class="dashboard-links">
        <a href="dashboard.php" class="dashboard-link">
          <div class="dashboard-card">
            <div class="card-icon">👤</div>
            <div class="card-text">ダッシュボード</div>
          </div>
        </a>
        <a href="staff.php" class="dashboard-link">
          <div class="dashboard-card">
            <div class="card-icon">👀</div>
            <div class="card-text">スタッフ画面表示</div>
          </div>
        </a>
        <a href="admin.php" class="dashboard-link">
          <div class="dashboard-card">
            <div class="card-icon">👀</div>
            <div class="card-text">トップページ</div>
          </div>
        </a>
        <a href="staff.php" class="dashboard-link">
          <div class="dashboard-card">
            <div class="card-icon">👀</div>
            <div class="card-text">動画管理</div>
          </div>
        </a>
      </div>
    </div>

    <div class="video-content">
      <h2>登録済み動画一覧</h2>
      <section class="admin-video-list">
        <ul id="adminVideoList">
          <!-- JavaScriptでリストを動的に追加 -->
        </ul>
      </section>
    </div>
  </div>
</main>

<?php include '../includes/footer.php'; ?>