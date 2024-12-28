<footer class="footer">
    <p>&copy; 2024 Synclyee. All Rights Reserved. v1.0.0</p>
  </footer>  
  <?php 
    if ($page !== 'index') {
      echo '<script type="module" src="../assets/js/main.js"></script>'; // $page が 'index' でない場合のみ main.js を読み込む
    }

    if (isset($page)) {
      if ($page === 'admin') {
        echo '<script type="module" src="../assets/js/admin.js" defer></script>';
      } elseif ($page === 'staff') {
        echo '<script type="module" src="../assets/js/staff.js" defer></script>';
      } elseif ($page === 'account') {
        echo '<script type="module" src="../assets/js/account.js" defer></script>';
        echo '<script type="module" src="../assets/js/password.js" defer></script>';
      } elseif ($page === 'videos-admin') {
        echo '<script type="module" src="../assets/js/videos-admin.js" defer></script>';
      }
    }
  ?>
</body>
</html>