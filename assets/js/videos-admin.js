import { openModal, closeModal } from './main.js';
import { fetchVideoApi, editVideoApi, deleteVideoApi, fetchSingleVideoApi, updateVideoOrderApi, getSortedVideoApi } from './videoApi.js';
import { fetchCategoryApi, addCategoryApi, updateCategoryOrderApi, getSortedCategoryApi } from './categoryApi.js';

// ページロード時に初期動画リストを取得
window.onload = async function() {
  try {
    const videos = await fetchVideoApi();
    // console.log('取得した動画データ:', videos);
    updateVideoList(videos);
  } catch (error) {
    console.error('エラーが発生しました:', error);
  }
};

// videos を category_id ベースでグループ化
function groupVideosByCategoryId(videos) {
  const groupedVideos = {};

  // カテゴリー名をキーとしているオブジェクトをループ
  Object.entries(videos).forEach(([categoryName, videoList]) => {
    videoList.forEach(video => {
      const categoryId = video.category_id; // category_id を取得
      if (!groupedVideos[categoryId]) {
        groupedVideos[categoryId] = []; // 初期化
      }
      groupedVideos[categoryId].push(video); // category_id ごとに追加
    });
  });

  return groupedVideos;
}


async function updateVideoList() {
  try {
    const categories = await getSortedCategoryApi(); 
    console.log('取得したカテゴリー順:', categories);
    const videos = await getSortedVideoApi(); // サーバーからソート済みの動画リストを取得
    console.log('取得した動画データ:', videos);

    // カテゴリーIDでグループ化
    const groupedVideos = groupVideosByCategoryId(videos);

    const adminList = document.getElementById('adminVideoList');
    adminList.innerHTML = ''; // リストをクリア
    
    // カテゴリー順にループ
    categories.forEach(category => {
      const { id: categoryId, name: categoryName } = category;

      const categorySection = document.createElement('div');
      categorySection.classList.add('category-section');
      categorySection.dataset.categoryId = categoryId;

      const categoryTitle = document.createElement('h3');
      categoryTitle.textContent = categoryName; // カテゴリー名を設定
      categorySection.appendChild(categoryTitle);

      const categoryList = document.createElement('ul');

      // 該当カテゴリーIDの動画リストを取得
      const videoList = groupedVideos[categoryId] || []; // 該当しない場合は空配列

      videoList.forEach(video => {
        const listItem = document.createElement('li');
        listItem.dataset.id = video.id;
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

        
      // Sortable.jsを各カテゴリーリストに適用
      new Sortable(categoryList, {
        group: { name: 'videos' }, // 他のリストにはドロップ不可
        animation: 150,
        draggable: 'li',
        onEnd: function () {
          console.log('onEnd イベントが発火しました');

          const newOrder = [...categoryList.querySelectorAll('li')].map(item => item.dataset.id);
          // console.log(`新しい${category}の順序:`, newOrder);

          if (newOrder.length === 0) {
            console.error('新しい順序が取得できませんでした');
            return;
          }
          // サーバーに新しい順序を送信
          updateVideoOrder(categoryId, newOrder);
        }
      });
    });

    // カテゴリー全体のSortableを適用
    new Sortable(adminList, {
      group: { name: 'categories', put: true },
      animation: 150,
      draggable: '.category-section',
      dataIdAttr: 'data-category-id',
      onStart: function () {
        console.log('onStart イベントが発火しました');
        const initialOrder = [...adminList.children].map(child => child.dataset.categoryId);
        console.log('開始時の順番:', initialOrder);
      },
      onEnd: function () {
        console.log('onEnd イベントが発火しました');
        // adminList の子要素を取得
        const updatedOrder = [...adminList.children];

        // 各要素の data-category-id を取得
        const newCategoryOrder = updatedOrder.map((child, index) => {
          return {
            categoryId: child.dataset.categoryId, // カテゴリーID
            position: index + 1 // インデックスをpositionとして設定（1始まり）
          };
        });

        // 順番を表示
        console.log('新しい順番:', newCategoryOrder);

        // 順番が取得できなかった場合
        if (newCategoryOrder.length === 0) {
          console.error('新しい順番が取得できませんでした');
          return;
        }

        // サーバーに新しいカテゴリー順序を送信
        updateCategoryOrder(newCategoryOrder);
      }
    });
                              
    // 削除と編集ボタンにイベントリスナーを追加
    adminList.addEventListener('click', (e) => {
      if (e.target.classList.contains('edit-btn')) {
        const videoId = e.target.dataset.id;
        editVideo(videoId);
      } else if (e.target.classList.contains('delete-btn')) {
        const videoId = e.target.dataset.id;
        deleteVideo(videoId);
      }
    });

  } catch (error) {
    console.error('動画リストの更新中にエラーが発生しました:', error);
  }
}



// サーバーに新しいカテゴリー順序を送信する関数
async function updateCategoryOrder(newCategoryOrder) {
  try {
    console.log('送信データ:', newCategoryOrder);
    const result = await updateCategoryOrderApi(newCategoryOrder); 
        
    console.log('サーバーからのレスポンス:', result);

    if (result?.success) {
      console.log('カテゴリー順序が正常に更新されました');
      updateVideoList(); // リストを再描画する関数

      } else {
        console.error(
          'カテゴリー順序の更新に失敗しました:', 
          result?.error || 'サーバーからエラー内容が返されませんでした'
        );
      }
    } catch (error) {
    console.error('カテゴリー順序更新中にエラーが発生しました:', error);
  }
}

// サーバーに新しい動画リストの順序を送信する関数
async function updateVideoOrder(category, order) {
  // console.log("送信するカテゴリー:", category);
  // console.log("送信する順序:", order);

  try {
    const result = await updateVideoOrderApi(category, order); 
    console.log(result);
    if (result.success) {
      console.log('順序が正常に更新されました');
      
      // 順序がサーバーに反映された後、ページを再描画
      updateVideoList(); // リストを再描画する関数
    } else {
      console.error('順序の更新に失敗しました:', result.error);
    }
  } catch (error) {
    console.error('エラーが発生しました:', error);
  }
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
    const categorySelect = document.getElementById('edit_video_category');
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

  let categoryId;

  console.log("新しいカテゴリー:", newCategory); // 新しいカテゴリーが入力されているか確認
  console.log("選択されたカテゴリー:", selectedCategory); // 選択されたカテゴリーのIDを確認

  // 新しいカテゴリーが入力されていれば、それをデータベースに追加
  if (newCategory) {
    console.log("新しいカテゴリーをAPIに送信:", newCategory); // 新しいカテゴリーのデータを確認
    const result = await addCategoryApi(newCategory); // カテゴリーを追加するAPI呼び出し
    console.log("カテゴリー追加の結果:", result); // APIレスポンスを確認

    if (result.success) {
      categoryId = result.category_id;  // 新しく追加されたカテゴリーIDを取得
      console.log("新しいカテゴリーID:", categoryId); // 新しいカテゴリーIDを確認
    } else {
      alert('カテゴリーの追加に失敗しました');
      return;
    }
  } else {
    // 既存のカテゴリーIDを使用
    categoryId = selectedCategory;
    console.log("既存のカテゴリーID:", categoryId); // 既存のカテゴリーIDを確認
  }

  categoryId = parseInt(categoryId, 10);

  const videoData = {
    video_id: videoId,
    title: title,
    url: url,
    category_id: categoryId,  // 新しいIDまたは既存のID
  };

  console.log("送信する動画データ:", videoData); // 送信する動画データを確認

  try {
    const result = await editVideoApi(videoData);
    console.log("動画更新の結果:", result); // APIレスポンスを確認

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
async function deleteVideo(videoId) {
  // 削除ボタンを無効化して重複クリックを防ぐ
  const deleteButton = document.querySelector(`button[data-id="${videoId}"]`);
  deleteButton.disabled = true;

  try {
    const result = await deleteVideoApi(videoId);

    if (result.success) {
      alert('動画が削除されました');
      const videos = await fetchVideoApi();
      updateVideoList(videos);  // 動画リストを再読み込み
    } else {
      alert('動画の削除に失敗しました');
    }
  } catch (error) {
    console.error('削除中にエラーが発生しました:', error);
    alert('削除中にエラーが発生しました');
  } finally {
    // 削除ボタンを再度有効化
    deleteButton.disabled = false;
  }
}

// 編集モーダルの閉じるボタン
document.querySelector('#editVideoModal .modal-close-btn').addEventListener('click', function() {
  closeModal('editVideoModal');
});

// 削除モーダルの閉じるボタン
document.querySelector('#deleteVideoModal .modal-close-btn').addEventListener('click', function() {
  closeModal('deleteVideoModal');
});

