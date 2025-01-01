export async function fetchCategoryApi() {
  const response = await fetch('../actions/categories/get_category.php');
  return response.json();
}

// 新しいカテゴリーを追加するAPI
export async function addCategoryApi(newCategory) {
  console.log("Sending category:", newCategory);
  try {
    console.log("Sending request to add category...");

    const response = await fetch('../actions/categories/add_category.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        name: newCategory,
      }),
    });

    console.log("Response status:", response.status);

    if (!response.ok) {
      const errorData = await response.json();
      console.error("Error response:", errorData); // エラーレスポンスを表示
      throw new Error('カテゴリーの追加に失敗しました');
    
    }

    const data = await response.json();
    console.log("Response data:", data);

    // レスポンスデータの確認
    if (data.error) {
      console.log("Server returned an error:", data.error);
      throw new Error(data.error); // サーバーからのエラーメッセージを表示
    }

    console.log("Category added:", data);
    return data; // カテゴリーIDを返す
  } catch (error) {
    console.error("Error in addCategoryApi:", error);
    throw new Error('カテゴリーの追加に失敗しました');
  }
}

export async function updateCategoryOrderApi(newCategoryOrder) {
  try {
    const response = await fetch('../actions/categories/update_category_order.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ order: newCategoryOrder }), // カテゴリー順序のみ送信
    });

      // レスポンスのステータスを確認
      if (!response.ok) {
        throw new Error(`HTTPエラー: ${response.status}`);
      }

      const data = await response.json();
      console.log('サーバーからのレスポンス:', data);

      if (data && data.success) {
        console.log('カテゴリー順序が正常に更新されました');
      } else {
        console.error('カテゴリー順序更新に失敗しました');
      }
      return data; // 呼び出し元に返す
    } catch (error) {
    console.error('カテゴリー順序更新中にエラーが発生しました:', error);
    throw error; // エラーを伝播
  }
}

//カテゴリーをポジション順にソート
export async function getSortedCategoryApi() {
    try {
      const response = await fetch('../actions/categories/get_sorted_categories.php');
      const data = await response.json();
      // console.log('サーバーから取得した動画データby sort:', data); // データを確認
      return data;
    } catch (error) {
      console.error('API呼び出しエラー:', error);
  }
}