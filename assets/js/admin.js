// ページロード時に初期動画リストを取得
window.onload = async function() {
  try {
    const response = await fetch('../actions/videos/get_video.php');
    const videos = await response.json();
    updateVideoList(videos);
    loadCategories();
  } catch (error) {
    console.error('エラーが発生しました:', error);
  }
};

async function addVideo() {
  const title = document.getElementById('videoTitle').value;
  const url = document.getElementById('videoUrl').value;
  const selectedCategory = document.getElementById('categorySelect').value;
  const newCategory = document.getElementById('newCategory').value.trim();

  // 入力バリデーション
  if (!title || !url) {
    alert("タイトルと URL は両方とも必須です!");
    return;
  }

  // カテゴリーが選択または新規追加されているか確認
  if (!selectedCategory && !newCategory) {
    alert('カテゴリーを選択または入力してください。');
    return;
  }

  try {
    // サーバーにデータを送信
    const response = await fetch('../actions/videos/add_video.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ title, url, selectedCategory, newCategory }),
    });

    if (!response.ok) {
      const errorData = await response.json();
      alert(`エラー: ${errorData.error}`);
      return;
    }

    // 最新の動画リストを取得
    const videos = await response.json();
    updateVideoList(videos);

    // 入力フォームをリセット
    document.getElementById('videoTitle').value = '';
    document.getElementById('videoUrl').value = '';
    // 新しいカテゴリーの入力欄を空にする
    document.getElementById('newCategory').value = ''; 

    // カテゴリーリストを即時更新
    await loadCategories();  // 動画追加後にカテゴリーリストを再取得
  } catch (error) {
    console.error('エラーが発生しました:', error);
  }
}

function updateVideoList(videos) {
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
        <input type="text" value="${video.url}" class="video-url" readonly>
        <button class="delete-btn" data-id="${video.id}">削除</button>`;
      categoryList.appendChild(listItem);
    });
    categorySection.appendChild(categoryList);

    // カテゴリーセクションを管理リストに追加
    adminList.appendChild(categorySection);
  });

  // 各動画の削除ボタンにイベントリスナーを追加
  const deleteButtons = document.querySelectorAll('.delete-btn');
  deleteButtons.forEach(button => {
    button.addEventListener('click', function () {
      const videoId = button.getAttribute('data-id'); // data-id属性からvideoIdを取得
      deleteVideo(videoId); // 削除処理
    });
  });
}

async function deleteVideo(videoId) {
  const isConfirmed = window.confirm('本当にこの動画を削除しますか？');

  console.log("削除対象の動画ID:", videoId);

  if (!isConfirmed) {
    return; // ユーザーがキャンセルした場合は処理を中断
  }

  try {
    const formData = { id: videoId }; // JSON形式でデータを送信

    const response = await fetch('../actions/videos/delete_video.php', { // `delete_video.php` を指定
      method: 'POST',
      body: JSON.stringify(formData), // JSONとして送信
      headers: {
        'Content-Type': 'application/json', // JSONで送信することを明示
      }
    });

    const result = await response.json(); // サーバーからのレスポンスをJSONとして取得
    console.log(result);

    // 削除後、動画リストを更新
    if (result.error) {
      alert('削除に失敗しました！');
    } else {
      alert('動画が削除されました！');
      updateVideoList(result); // 新しい動画リストを反映
      await loadCategories();
    }
  } catch (error) {
    console.error("削除に失敗しました:", error);
  }
}

async function loadVideoList() {
  try {
    const response = await fetch('../actions/videos/get_video.php');
    const videos = await response.json();
    updateVideoList(videos); // 取得した動画リストを更新
  } catch (error) {
    console.error('エラーが発生しました:', error);
  }
}

// サーバーからカテゴリー一覧を取得
async function loadCategories() {
  try {
    const response = await fetch('../actions/categories/get_category.php'); // カテゴリー取得APIを呼び出し
    const categories = await response.json();

    // エラーチェック
    if (response.ok) {
      const categorySelect = document.getElementById('categorySelect');
      // 最初にデフォルトの空オプションを設定
      categorySelect.innerHTML = '<option value="">-- カテゴリーを選択 --</option>';

      // 取得したカテゴリーを <option> として追加
      categories.forEach(category => {
        const option = document.createElement('option');
        option.value = category.category;
        option.textContent = category.category;
        categorySelect.appendChild(option);
      });
    } else {
      alert("カテゴリーの取得に失敗しました。");
    }
  } catch (error) {
    console.error('エラーが発生しました:', error);
    alert("カテゴリーの取得中にエラーが発生しました。");
  }
}
