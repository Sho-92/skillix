// ユーザー情報を取得するAPI
export async function fetchUserApi() {
  try {
    const response = await fetch(`../actions/users/get_user.php`);
    if (!response.ok) {
      throw new Error(`Failed to fetch user: ${response.status}`);
    }
    return response;
  } catch (error) {
    console.error('Error in fetchUserApi:', error);
    throw error;
  }
}

// 新しいユーザーを追加するAPI
export async function addUserApi(userData) {
  try {
    const response = await fetch('../actions/users/add_user.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(userData), // JSON形式でデータを送信
    });

    if (!response.ok) {
      const errorText = await response.text();
      throw new Error(`Failed to add user: ${response.status} - ${errorText}`);
    }

    const result = await response.json();
    return result;
  } catch (error) {
    console.error('Error in addUserApi:', error);
    throw error;
  }
}

// ユーザーを編集するAPI
export async function editUserApi(userData) {
  try {
    const response = await fetch('../actions/users/edit_user.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(userData),
    });
    if (!response.ok) {
      throw new Error(`Failed to edit user: ${response.status}`);
    }
    return await response.json();
  } catch (error) {
    console.error('Error in editUserApi:', error);
    throw error;
  }
}

// ユーザーを削除するAPI
export async function deleteUserApi(userId) {
  try {
    const response = await fetch('../actions/users/delete_user.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ user_id: userId }),
    });

    if (!response.ok) {
      throw new Error(`Failed to delete user: ${response.status}`);
    }

    const result = await response.json();  // JSON形式で受け取る

    if (result.success) {
      return result;  // 成功の場合、結果を返す
    } else {
      throw new Error(result.message || 'Unknown error');
    }
  } catch (error) {
    console.error('Error in deleteUserApi:', error);
    throw error;
  }
}

// 単一ユーザー情報を取得するAPI
export async function fetchSingleUserApi(userId) {
  try {
    const response = await fetch(`../actions/users/get_single_user.php?id=${userId}`);
    if (!response.ok) {
      throw new Error(`Failed to fetch single user: ${response.status}`);
    }
    return await response.json();
  } catch (error) {
    console.error('Error in fetchSingleUserApi:', error);
    throw error;
  }
}
