const AUTO_HIDE_TIME = 1000; // 1秒

let passwordTimeout;
let confirmPasswordTimeout;

// パスワード表示のトグル
document.getElementById('toggle-password').addEventListener('click', function () {
  const passwordField = document.getElementById('password');
  const type = passwordField.type === 'password' ? 'text' : 'password';
  passwordField.type = type;

  // 既存のタイマーをクリアして再設定
  clearTimeout(passwordTimeout);

  // 一定時間後にパスワードを非表示にする
  passwordTimeout = setTimeout(function () {
    passwordField.type = 'password';
  }, AUTO_HIDE_TIME);
});

// 確認用パスワード表示のトグル
document.getElementById('toggle-password-confirm').addEventListener('click', function () {
  const confirmPasswordField = document.getElementById('password_confirm');
  const type = confirmPasswordField.type === 'password' ? 'text' : 'password';
  confirmPasswordField.type = type;

  // 既存のタイマーをクリアして再設定
  clearTimeout(confirmPasswordTimeout);

  // 一定時間後に確認用パスワードを非表示にする
  confirmPasswordTimeout = setTimeout(function () {
    confirmPasswordField.type = 'password';
  }, AUTO_HIDE_TIME);
});

