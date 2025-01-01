import { fetchVideoApi, addVideoApi } from './videoApi.js';
import { fetchCategoryApi, addCategoryApi } from './categoryApi.js';

// ページロード時に初期動画リストを取得
window.onload = async function() {
  try {
    const videos = await fetchVideoApi();
    updateVideoList(videos);
    loadCategories();  // カテゴリーリストをロード
  } catch (error) {
    console.error('エラーが発生しました:', error);
  }
};

document.getElementById('videoForm').addEventListener('submit', async function (event) {
  event.preventDefault(); // デフォルトのフォーム送信を防ぐ

  // フォームデータを取得
  const form = event.target;
  const formData = new FormData(form);
  const title = formData.get('title');
  const url = formData.get('url');
  const selectedCategory = formData.get('category');
  const newCategory = formData.get('new_category').trim();

  // 入力バリデーション
  if (!title || !url) {
    alert("タイトルと URL は両方とも必須です!");
    return;
  }

  if (!selectedCategory && !newCategory) {
    alert('カテゴリーを選択または入力してください。');
    return;
  }

  // カテゴリーIDを取得
  let categoryId = null;

  if (selectedCategory) {
    categoryId = selectedCategory; // 選ばれたカテゴリーIDを使用
  } else if (newCategory) {
    // 新しいカテゴリーを追加する処理
    const categoryResponse = await addCategoryApi(newCategory);
    console.log('New Category Response:', categoryResponse);
    categoryId = parseInt(categoryResponse.id, 10); /// idを数値に変換
  }

  // 新しい動画のデータ
  const videoData = {
    title,
    url,
    category_id: parseInt(categoryId, 10), // category_id を送信
  };
  console.log('Video Data:', videoData); // 送信するデータを確認

  try {
    // 動画の追加処理
    const videos = await addVideoApi(videoData);
    console.log(videos); // ここで videos の中身を確認

    updateVideoList(videos);

    // フォームをリセット
    form.reset();

    // カテゴリーと動画リストの即時更新
    await loadCategories();
    await loadVideoList();
  } catch (error) {
    console.error('エラーが発生しました:', error);
  }
});

// 動画リストの更新
function updateVideoList(videos) {
  if (!Array.isArray(videos)) {
    console.error('videos is not an array:', videos);
    return;
  }

  const adminList = document.getElementById('adminVideoList');
  adminList.innerHTML = ''; // リストをクリア

  // 動画が登録されていない場合のメッセージを表示
  if (videos.length === 0) {
    const noVideosMessage = document.createElement('p');
    noVideosMessage.textContent = '登録されている動画はありません。';
    adminList.appendChild(noVideosMessage);
    return; // これ以上処理を続けない
  }

  // カテゴリーごとに動画をグループ化
  const categorizedVideos = videos.reduce((groups, video) => {
    const category = video.category || '未分類'; // カテゴリーがない場合は "未分類"
    if (!groups[category]) {
      groups[category] = [];
    }
    groups[category].push(video);
    return groups;
  }, {});

  // 各カテゴリーをセクション化して表示
  Object.keys(categorizedVideos).forEach(category => {
    // カテゴリーのセクションを作成
    const categorySection = document.createElement('div');
    categorySection.classList.add('category-section');

    // カテゴリーのタイトルを追加
    const categoryTitle = document.createElement('h3');
    categoryTitle.textContent = category;
    categorySection.appendChild(categoryTitle);

    // カテゴリー内の動画リストを追加
    const categoryList = document.createElement('ul');
    categorizedVideos[category].forEach(video => {
      const listItem = document.createElement('li');
      listItem.innerHTML = `
        <strong>${video.title}</strong><br>
        `;
      categoryList.appendChild(listItem);
    });
    categorySection.appendChild(categoryList);

    // カテゴリーセクションを管理リストに追加
    adminList.appendChild(categorySection);
  });
}

// 動画リストを再読み込み
async function loadVideoList() {
  try {
    const videos = await fetchVideoApi();
    updateVideoList(videos); // 取得した動画リストを更新
  } catch (error) {
    console.error('エラーが発生しました:', error);
  }
}

// カテゴリー一覧をロードしてセレクトボックスに反映する関数
async function loadCategories() {
  try {
    const categories = await fetchCategoryApi();
    console.log("Fetched categories:", categories);

    // カテゴリーが空の場合の処理
    if (!categories || categories.length === 0) {
      console.warn("No categories found.");
      return;
    }

    // セレクトボックスを取得
    const categorySelect = document.getElementById('categorySelect');
    if (!categorySelect) {
      console.error("Category select element not found.");
      return;
    }

    // デフォルトオプションを初期化
    categorySelect.innerHTML = '<option value="">-- カテゴリーを選択 --</option>';

    // カテゴリーをループしてオプションに追加
    categories.forEach(category => {
      const option = document.createElement('option');
      option.value = category.id; // カテゴリーIDをvalueに設定
      option.textContent = category.name; // カテゴリー名を表示
      categorySelect.appendChild(option);
    });

    console.log("Category select updated successfully.");
  } catch (error) {
    console.error('エラーが発生しました:', error);
    alert("カテゴリーの取得中にエラーが発生しました。");
  }
}
