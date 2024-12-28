<?php
$menuItems = [
  ["url" => "admin.php", "icon" => "🏠", "text" => "トップページ"],
  ["url" => "account.php", "icon" => "👤", "text" => "ユーザー管理"],
  ["url" => "videos-admin.php", "icon" => "🎥", "text" => "動画管理"],
  ["url" => "staff-view.php", "icon" => "👀", "text" => "スタッフ画面表示"],
];
?>

<div class="sidebar-content">
  <h2 class="sidebar-title">メインメニュー</h2>
  <div class="sidebar-links">
    <?php foreach ($menuItems as $item): ?>
      <a href="<?= $item['url'] ?>" class="sidebar-link">
        <div class="sidebar-card">
          <div class="sidebar-card-icon"><?= $item['icon'] ?></div>
          <div class="sidebar-card-text"><?= $item['text'] ?></div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</div>
