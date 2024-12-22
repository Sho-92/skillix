<footer class="footer">
    <p>&copy; 2024 Synclyee. All Rights Reserved. v1.0.0</p>
  </footer>  
  <?php 
    if ($page !== 'index') {
      echo '<script src="../assets/js/main.js"></script>'; // $page が 'index' でない場合のみ main.js を読み込む
    }

    if (isset($page)) {
      if ($page === 'admin') {
        echo '<script src="../assets/js/admin.js" defer></script>';
      } elseif ($page === 'staff') {
        echo '<script src="../assets/js/staff.js" defer></script>';
      } elseif ($page === 'dashboard') {
        echo '<script src="../assets/js/dashboard.js" defer></script>';
      }
    }
  ?>
</body>
</html>