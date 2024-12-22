async function addVideo() {
  const title = document.getElementById('videoTitle').value;
  const url = document.getElementById('videoUrl').value;

  if (!title || !url) {
    alert("タイトルと URL は両方とも必須です!");
    return;
  }

  try {
    // サーバーにデータを送信
    const response = await fetch('../actions/add_video.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ title, url }),
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
  } catch (error) {
    console.error('エラーが発生しました:', error);
  }
}

function updateVideoList(videos) {
  const adminList = document.getElementById('adminVideoList');
  adminList.innerHTML = ''; // リストをクリア

  // 新しい動画リストを表示
  videos.forEach(video => {
    const listItem = document.createElement('li');
    listItem.innerHTML = `
      <strong>${video.title}</strong><br>
      <input type="text" value="${video.url}" class="video-url" readonly>
      <button class="delete-btn" data-id="${video.id}">削除</button>`;    
      adminList.appendChild(listItem);
  });

  const deleteButtons = document.querySelectorAll('.delete-btn');
  deleteButtons.forEach(button => {
    button.addEventListener('click', function() {
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

    const response = await fetch('../actions/delete_video.php', { // `delete_video.php` を指定
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
    }
  } catch (error) {
    console.error("削除に失敗しました:", error);
  }
}

async function loadVideoList() {
  try {
    const response = await fetch('../actions/get_video.php');
    const videos = await response.json();
    updateVideoList(videos); // 取得した動画リストを更新
  } catch (error) {
    console.error('エラーが発生しました:', error);
  }
}

// ページロード時に初期動画リストを取得
window.onload = async function() {
  try {
    const response = await fetch('../actions/get_video.php');
    const videos = await response.json();
    updateVideoList(videos);
  } catch (error) {
    console.error('エラーが発生しました:', error);
  }
};
