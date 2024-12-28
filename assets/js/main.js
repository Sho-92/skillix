// logout ボタン
document.querySelector('.logout').addEventListener('click', function() {
  // ログアウト処理のためにlogout.phpにリダイレクト
  window.location.href = '../auth/logout.php'; // リダイレクト先を指定
});

// modal.js (共通モジュール)
export function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.style.display = 'block';
  }
}

export function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.style.display = 'none';
  }
}

