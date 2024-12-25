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
  <h2>最新動画一覧</h2>
  <section class="staff-video-list">
    <ul id="staffVideoList">
      <!-- JavaScriptでリストを動的に追加 -->
    </ul>
  </section>
  <section class="staff-video-list">
    <h2>問診動画リスト</h2>
    <ul id="staffVideoList">
      <!-- JavaScriptでリストを動的に追加 -->
    </ul>
  </section>
  <section class="staff-video-list">
    <h2>施術動画リスト</h2>
    <ul id="staffVideoList">
      <!-- JavaScriptでリストを動的に追加 -->
    </ul>
  </section>
</main>

<?php include '../includes/footer.php'; ?>
