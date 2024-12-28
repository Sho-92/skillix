<?php
  session_start(); // セッションを開始

  require_once '../includes/functions.php'; 
  checkAccess(); // スタッフと本社の両方にアクセス許可
  
  require_once '../includes/db.php'; // DB接続ファイルを読み込み
 
  // 動画一覧を取得
  $stmt = $pdo->query("SELECT title, url FROM videos ORDER BY created_at DESC");
  $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // JSONを出力（API用）
  if (isset($_GET['api']) && $_GET['api'] == 'true') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($videos);
    exit();
  }

  $page = 'staff'; // ページ名設定
?>

<?php include '../includes/header.php'; ?>

<main class="main">
  <h2>スタッフ用：動画視聴セクション</h2>
  <!-- 最新動画セクション -->
  <section class="video-section">
    <h2>最新動画</h2>
    <ul id="latestVideoList" class="video-list"></ul>
  </section>

  <!-- カテゴリー別セクション -->
  <section id="categorySections">
    <!-- JavaScriptでカテゴリーごとのセクションを生成 -->
  </section>
</main>

<?php include '../includes/footer.php'; ?>
