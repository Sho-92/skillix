export async function fetchVideoApi() {
  try {
    const response = await fetch('../actions/videos/get_video.php');
    if (response.ok) {
      const videos = await response.json();  // サーバーからのレスポンスを取得
      return videos;  // サーバーから取得したデータを返す
    } else {
      throw new Error('動画リストの取得に失敗しました');
    }
  } catch (error) {
    console.error('動画リスト取得中にエラーが発生しました:', error);
  }
}

export async function addVideoApi(videoData) {
  const response = await fetch('../actions/videos/add_video.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(videoData),
  });
  return response.json();
}

export async function editVideoApi(videoData) {
  const response = await fetch('../actions/videos/edit_video.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(videoData),
  });
  return response.json();
}

export async function deleteVideoApi(videoId) {
  console.log('削除対象の動画ID:', videoId); 
  const response = await fetch('../actions/videos/delete_video.php', {
    method: 'DELETE',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ video_id: videoId }),
  });
  const result = await response.json();
  console.log('APIのレスポンス:', result);  // ここでレスポンスを確認
  return result;
}

export async function fetchSingleVideoApi(videoId) {
  const response = await fetch(`../actions/videos/get_single_video.php?id=${videoId}`);
  return response.json();
}

export async function updateVideoOrderApi(category, order) {
  try {
    // console.log("API送信データ:", { category, order }); // サーバーに送信するデータを確認

    const response = await fetch('../actions/videos/update_video_order.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ category, order }),
    });
    // console.log("サーバー応答ステータス:", response.status); // サーバーのHTTPステータスを確認
    if (!response.ok) {
      throw new Error(`HTTPエラー: ${response.status}`); // HTTPエラーの場合は例外をスロー
    }

    const data = await response.json();
    // console.log("サーバー応答データ:", data); // サーバーからのデータを確認

    if (!data.success) {
      throw new Error(`サーバーエラー: ${data.error || '不明なエラー'}`);
    }

    return data;
  } catch (error) {
    console.error("updateVideoOrderApiでエラーが発生:", error); // エラーログを出力
    throw error; // エラーを再スロー
  }
}


//動画をポジション順にソート
export async function getSortedVideoApi() {
    try {
      const response = await fetch('../actions/videos/get_sorted_videos.php');
      const data = await response.json();
      // console.log('サーバーから取得した動画データby sort:', data); // データを確認
      return data;
    } catch (error) {
      console.error('API呼び出しエラー:', error);
  }
}

// 最新動画順にソート
export async function fetchLatestVideoApi() {
  try {
    // 最新動画を取得するAPIエンドポイントを呼び出す
    const response = await fetch('../actions/videos/get_latest_videos.php');
    
    // レスポンスが正常かをチェック
    if (!response.ok) {
      throw new Error('動画の取得に失敗しました');
    }
    
    // JSONレスポンスを取得
    const videos = await response.json();
    
    // 取得した動画データを返す
    return videos;
  } catch (error) {
    console.error('エラーが発生しました:', error);
    throw error; // エラーを再スローして、呼び出し元でハンドリングできるようにする
  }
}

  