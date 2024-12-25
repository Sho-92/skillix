<?php
  $page = 'index'; 
?>

<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Synclyeeは社員教育用のEラーニングプラットフォームです。">
    <title>Synclyee (シンクリー) - Admin Panel</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/index.css">
  </head>

  <main class="main">
    <h1>Synclyee</h1>
    <section class="login-form">
      <form method="post" action="auth/login.php">
          <label for="employee_id">従業員ID</label>
          <input type="text" name="employee_id" id="employee_id" placeholder="Employee ID" required>

          <label for="password">パスワード</label>
          <input type="password" name="password" id="password" placeholder="Password" required>

          <button type="submit" name="login">ログイン</button>
      </form>
    </section>
  </main>

<?php include './includes/footer.php'; ?>
