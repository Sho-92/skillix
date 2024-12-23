let userIdToDelete = null; // 削除ボタンを押すたびに異なるユーザーIDを格納するため「let」

// 削除ボタン
function deleteUser(userId) {
  userIdToDelete = userId;
  document.getElementById('deleteModal').style.display = 'block';
}

// 削除確認モーダル「Yes」操作
function confirmDelete() {
  if (userIdToDelete !== null) {
    deleteUserFromDatabase(userIdToDelete);
  }
}

// 削除処理
function deleteUserFromDatabase(userId) {
  fetch('../actions/delete_user.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'user_id=' + userId,
  })
  .then(response => {
    if (response.ok) {
      // 成功した場合、モーダルを閉じてページをリロード
      closeModal('deleteModal');
      location.reload(); // ユーザーリストを再読み込み
    } else {
      alert('Error deleting user!');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('ユーザーの削除中にエラーが発生しました!');
  });
}

// 編集ボタン
function editUser(userId) {
  fetch('../actions/get_user.php?user_id=' + userId)
    .then(response => response.json())
    .then(user => {
      if (user.error) {
        console.log('Error:', user.error);
        return;
      }
      // モーダルにユーザー情報をセット
      document.getElementById('edit_user_id').value = user.id;
      document.getElementById('edit_username').value = user.username;
      document.getElementById('edit_employee_id').value = user.employee_id;
      document.getElementById('edit_role').value = user.role;
      // モーダルを表示
      document.getElementById('editModal').style.display = 'block';
    })
    .catch(error => {
      console.error('Error fetching user data:', error);
    });
}

// 編集フォームの送信処理
document.getElementById('editUserForm').addEventListener('submit', function(event) {
  event.preventDefault();  // フォーム送信のデフォルト動作をキャンセル

  const formData = new FormData(this);

  // fetchで編集リクエストを送信
  fetch('../actions/edit_user.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.text())
  .then(message => {
    alert(message);  // 成功メッセージを表示
    location.reload();  // ページをリロードして反映
  })
  .catch(error => console.error('Error:', error));
});

// モーダルを閉じる
function closeModal(modalId) {
  document.getElementById(modalId).style.display = 'none';
}
