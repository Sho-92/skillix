<?php
session_start(); // セッションを開始

require_once '../includes/functions.php'; // functions.php をインクルードして、checkAccess関数を使用できるようにする
// 本社ユーザー専用ページを確認
checkAccess('admin'); // 本社のみアクセス許可

$page = 'admin'; // ページ名設定
$pageHasSidebar = true; // サイドバーが必要なページ

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
  <div class="dashboard-content">

    <?php include '../pages/sidebar.php'; ?>
    
    <div class="main-content">
      <h2>動画登録フォーム</h2>
      <form id="videoForm">
        <section class="input-video">
          <input type="text" id="videoTitle" name="title" class="video-title" placeholder="タイトルを入力" required>
          <input type="text" id="videoUrl" name="url" class="video-url" placeholder="YouTube URL" required>
          <div class="category-input">
            <label for="categorySelect">カテゴリーを選択または追加:</label>
            <select id="categorySelect" name="category" class="category-select">
              <option value="">-- カテゴリーを選択 --</option>
              <!-- 動的に追加される既存のカテゴリー -->
            </select>
            <input type="text" id="newCategory" name="new_category" class="new-category" placeholder="新しいカテゴリーを追加">
          </div>
          <button type="submit" name="add_video">追加</button>
        </section>
      </form>

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