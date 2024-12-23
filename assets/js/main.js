// logout ボタン
document.querySelector('.logout').addEventListener('click', function() {
  // ログアウト処理のためにlogout.phpにリダイレクト
  window.location.href = '../auth/logout.php'; // リダイレクト先を指定
});