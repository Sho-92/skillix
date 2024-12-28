import { openModal, closeModal } from './main.js';
import { fetchUserApi, addUserApi, editUserApi, deleteUserApi, fetchSingleUserApi } from './userApi.js';

// ページロード時に初期動画リストを取得
window.onload = async function() {
  try {
    const users = await fetchUserApi();
    loadUserList(users);
  } catch (error) {
    console.error('エラーが発生しました:', error);
  }
};

document.getElementById('userAddForm').addEventListener('submit', async function (event) {
  event.preventDefault(); // デフォルトのフォーム送信を防ぐ

  // フォームデータを取得
  const form = event.target;
  const formData = new FormData(form);

  // FormData を JSON に変換
  const userData = {
    username: formData.get('username'),
    employee_id: formData.get('employee_id'),
    password: formData.get('password'),
    password_confirm: formData.get('password_confirm'),
    role: formData.get('role'),
  };

  try {
    // 非同期でユーザーを追加
    const result = await addUserApi(userData); 

    if (result.success) {
      alert('ユーザーが正常に追加されました！');
      form.reset(); // フォームをリセット
      await loadUserList(); // ユーザーリストの再読み込み
    } else {
      alert('ユーザー追加エラー: ' + result.message);
    }
  } catch (error) {
    console.error('エラーが発生しました:', error);
    alert('エラー: ' + error.message);
  }
});

// ユーザーリストをリロードする関数
async function loadUserList() {
  try {
    const response = await fetch('../actions/users/get_user.php');
    const users = await response.json();

    // ユーザーリストが取得できているか確認
    console.log('取得したユーザーリスト:', users);

    if (Array.isArray(users)) { // 配列かどうかを確認
      const userTableBody = document.getElementById('userTableBody');
      userTableBody.innerHTML = '';  // テーブルをリセット

      users.forEach(user => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${user.username}</td>
          <td>${user.employee_id}</td>
          <td>${user.password}</td>
          <td>${user.role}</td>
          <td class="actions">
            <button class="edit-btn" data-id="${user.id}">編集</button>
            <button class="delete-btn" data-id="${user.id}">削除</button>
          </td>
        `;
        userTableBody.appendChild(row);
      });
    } else {
      console.error('ユーザーリストが配列ではありません:', users);
      alert('ユーザーリストの取得に失敗しました。');
    }
  } catch (error) {
    console.error('ユーザーリストの読み込みエラー:', error);
    alert('ユーザーリストの読み込みに失敗しました。');
  }

  // 削除と編集ボタンにイベントリスナーを追加
  document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', () => editUser(button.dataset.id));
  });

  document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', () => deleteUser(button.dataset.id));
  });


}

// 編集ボタンがクリックされた時にモーダルを開く
async function editUser(userId) {
  try {
    // サーバーからユーザー情報を取得 (API呼び出し)
    const user = await fetchSingleUserApi(userId);

    // モーダルにユーザー情報をセット
    document.getElementById('edit_user_id').value = user.id;
    document.getElementById('edit_username').value = user.username;
    document.getElementById('edit_employee_id').value = user.employee_id;
    document.getElementById('edit_role').value = user.role;

    // モーダルを表示
    openModal('editUserModal');
  } catch (error) {
    console.error('ユーザー情報の取得に失敗しました:', error);
    alert('ユーザー情報の取得に失敗しました。');
  }
}

// ユーザー情報の編集を保存
async function updateUser(event) {
  event.preventDefault(); // フォーム送信のデフォルト動作をキャンセル

  const userId = document.getElementById('edit_user_id').value;
  const username = document.getElementById('edit_username').value;
  const employeeId = document.getElementById('edit_employee_id').value;
  const password = document.getElementById('password').value;
  const passwordConfirm = document.getElementById('password_confirm').value;
  const role = document.getElementById('edit_role').value;

  if (password !== passwordConfirm) {
    alert("パスワードが一致しません。");
    return;
  }

  const userData = {
    user_id: userId,
    username: username,
    employee_id: employeeId,
    password: password,
    role: role,
  };

  try {
    // サーバーでユーザー情報を更新
    const result = await editUserApi(userData);

    if (result.success) {
      alert('ユーザー情報が正常に更新されました');
      // ユーザーリストを更新
      const users = await fetchUserApi();
      loadUserList(users);  // ユーザーリストの再表示
      closeModal('editUserModal');
    } else {
      alert('ユーザー情報の更新に失敗しました');
    }
  } catch (error) {
    console.error('エラーが発生しました:', error);
    alert('ユーザー情報の更新中にエラーが発生しました');
  }
}

// ユーザー削除確認モーダルを表示
async function deleteUser(userId) {
  openModal('deleteUserModal');

  // まず、削除ボタンのイベントリスナーをリセット
  const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
  confirmDeleteBtn.removeEventListener('click', handleDeleteClick);  // 既存のイベントリスナーを削除

  // 削除ボタンに新しいイベントリスナーを設定
  confirmDeleteBtn.addEventListener('click', handleDeleteClick);

  // 削除処理を行う関数
  async function handleDeleteClick() {
    try {
      const result = await deleteUserApi(userId);
      
      if (result.success) {
        alert('ユーザーが削除されました');
        // ユーザーリストを再取得
        const users = await fetchUserApi();
        loadUserList(users);  // ユーザーリストの再表示
        closeModal('deleteUserModal');
      } else {
        alert('ユーザー削除に失敗しました');
      }
    } catch (error) {
      console.error('削除中にエラーが発生しました:', error);
      alert('削除中にエラーが発生しました');
    }
  }
}

// モーダルの閉じるボタンにイベントリスナーを追加
document.querySelectorAll('.modal-close-btn').forEach(button => {
  button.addEventListener('click', function() {
    closeModal('editUserModal');
    closeModal('deleteUserModal');
  });
});

// 編集フォームの送信
document.getElementById('editUserForm').addEventListener('submit', updateUser);
