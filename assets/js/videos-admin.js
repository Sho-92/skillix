import { openModal, closeModal } from './main.js';
import { fetchVideoApi, editVideoApi, deleteVideoApi, fetchSingleVideoApi} from './videoApi.js';
import { fetchCategoryApi } from './categoryApi.js';

// ページロード時に初期動画リストを取得
window.onload = async function() {
  try {
    const videos = await fetchVideoApi();
    updateVideoList(videos);
  } catch (error) {
    console.error('エラーが発生しました:', error);
  }
};

function updateVideoList(videos) {
  const adminList = document.getElementById('adminVideoList');
  adminList.innerHTML = ''; // リストをクリア

  // カテゴリーごとに動画をグループ化
  const categorizedVideos = videos.reduce((groups, video) => {
    const category = video.category || '未分類';
    if (!groups[category]) {
      groups[category] = [];
    }
    groups[category].push(video);
    return groups;
  }, {});

  // 各カテゴリーをセクション化して表示
  Object.keys(categorizedVideos).forEach(category => {
    const categorySection = document.createElement('div');
    categorySection.classList.add('category-section');

    const categoryTitle = document.createElement('h3');
    categoryTitle.textContent = category;
    categorySection.appendChild(categoryTitle);

    const categoryList = document.createElement('ul');
    categorizedVideos[category].forEach(video => {
      const listItem = document.createElement('li');
      listItem.innerHTML = `
        <strong>${video.title}</strong><br>
        <input type="text" value="${video.url}" class="video-url" readonly>
        <button class="edit-btn" data-id="${video.id}">編集</button>
        <button class="delete-btn" data-id="${video.id}">削除</button>
      `;
      categoryList.appendChild(listItem);
    });
    categorySection.appendChild(categoryList);
    adminList.appendChild(categorySection);
  });

  // 削除と編集ボタンにイベントリスナーを追加
  document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', () => editVideo(button.dataset.id));
  });

  document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', () => deleteVideo(button.dataset.id));
  });

}

// 編集ボタン
async function editVideo(videoId) {
  try {
    // 特定の動画情報を取得
    const video = await fetchSingleVideoApi(videoId);

    // モーダルにデータをセット
    document.getElementById('edit_video_id').value = video.id;
    document.getElementById('edit_video_title').value = video.title;
    document.getElementById('edit_video_url').value = video.url;

    // カテゴリーセレクトボックスにカテゴリーを追加
    const categorySelect = document.getElementById('edit_video_category');
    categorySelect.innerHTML = `<option value="">-- カテゴリーを選択 --</option>`;
    categorySelect.innerHTML += `<option value="${video.category}" selected>${video.category}</option>`;

    // カテゴリーを読み込んで表示
    await loadCategories();
    // モーダルを表示
    openModal('editVideoModal');
  } catch (error) {
    console.error('編集情報の取得に失敗しました:', error);
    alert('編集情報の取得に失敗しました。');
  }
}

async function loadCategories() {
  try {
    // サーバーからカテゴリーを取得
    const categories = await fetchCategoryApi();

    const categorySelect = document.getElementById('edit_video_category');
    categorySelect.innerHTML = '<option value="">-- カテゴリーを選択 --</option>'; // 初期状態として空オプションを設定

    // 取得したカテゴリーを <option> として追加
    categories.forEach(category => {
      const option = document.createElement('option');
      option.value = category.category;
      option.textContent = category.category;
      categorySelect.appendChild(option);
    });
  } catch (error) {
    console.error('カテゴリーの取得に失敗しました:', error);
    alert('カテゴリーの取得に失敗しました。');
  }
}

// 編集ボタンにイベントリスナーを追加
document.querySelectorAll('.edit-btn').forEach(button => {
  button.addEventListener('click', (e) => {
    const videoId = e.target.dataset.id;  // ボタンに設定されたデータIDを取得
    editVideo(videoId);  // モーダルを開いて編集情報をセット
  });
});

// 保存ボタン
async function updateVideo() {
  const videoId = document.getElementById('edit_video_id').value;
  const title = document.getElementById('edit_video_title').value;
  const url = document.getElementById('edit_video_url').value;

  // 新しいカテゴリーが入力されているかどうかを確認
  const newCategory = document.getElementById('newCategory').value.trim();
  const selectedCategory = document.getElementById('edit_video_category').value;

  // 新しいカテゴリーが入力されていれば、それを使用
  const category = newCategory || selectedCategory;

  const videoData = {
    video_id: videoId,
    title: title,
    url: url,
    category: category,
  };

  try {
    const result = await editVideoApi(videoData);

    if (result.success) {
      alert('動画が正常に更新されました');
      const videos = await fetchVideoApi();
      updateVideoList(videos);  // 動画リストを更新
      closeModal('editVideoModal'); // モーダルを閉じる
    } else {
      alert('動画の更新に失敗しました');
    }
  } catch (error) {
    console.error('エラーが発生しました:', error);
    alert('動画の更新中にエラーが発生しました');
  }
}

document.getElementById('editVideoForm').addEventListener('submit', async (event) => {
  event.preventDefault(); // フォームがリロードされないように防ぐ
  await updateVideo(); // 動画更新処理を実行
});

// 削除ボタンをクリックしたときの処理
function deleteVideo(videoId) {
  // モーダルを表示
  openModal('deleteVideoModal');

  // 確認ボタンのクリックイベントを設定
  document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
    try {
      // 削除APIを呼び出す
      const result = await deleteVideoApi(videoId);

      // 削除が成功した場合
      if (result.success) {
        alert('動画が削除されました');
        const videos = await fetchVideoApi();
        updateVideoList(videos);  // 動画リストを再読み込み
        closeModal('deleteVideoModal');  // モーダルを閉じる
      } else {
        alert('動画の削除に失敗しました');
      }
    } catch (error) {
      console.error('削除中にエラーが発生しました:', error);
      alert('削除中にエラーが発生しました');
    }
  });
}

// 編集モーダルの閉じるボタン
document.querySelector('#editVideoModal .modal-close-btn').addEventListener('click', function() {
  closeModal('editVideoModal');
});

// 削除モーダルの閉じるボタン
document.querySelector('#deleteVideoModal .modal-close-btn').addEventListener('click', function() {
  closeModal('deleteVideoModal');
});