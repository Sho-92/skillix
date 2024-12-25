// ページロード時に初期動画リストを取得
window.onload = async function() {
  try {
    // PHPから動画リストを取得
    const response = await fetch('../actions/videos/get_video.php');
    console.log("サーバーからのレスポンス:", response);
    
    // JSON形式で動画リストをパース
    const videos = await response.json();
    console.log('取得した動画データ:', videos);

    // 動画リストを表示
    updateVideoList(videos);
  } catch (error) {
    console.error('エラーが発生しました:', error);
  }
};

// 動画リストをHTMLに更新する関数
function updateVideoList(videos) {
  const staffVideoList = document.getElementById('staffVideoList');
  staffVideoList.innerHTML = ''; // 既存のリストをクリア

  // 動画情報をリストとして追加
  videos.forEach(video => {
    const listItem = document.createElement('li');
    listItem.innerHTML = `
      <div class="video-item">
      <strong class="video-title">${video.title}</strong><br>
      <iframe class="video-iframe" src="${video.url}" frameborder="0" allowfullscreen></iframe><br>
      `;
    staffVideoList.appendChild(listItem);
  });
}

console.log("現在のURL:", window.location.href);
console.log("リクエスト先:", '../actions/videos/get_video.php');

