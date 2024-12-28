import { fetchVideoApi } from './videoApi.js';

// ページロード時にデータを取得
// ページロード時にデータを取得
window.onload = async function() {
  try {
    // 動画データを取得
    const videos = await fetchVideoApi();

    const NUM_LATEST_VIDEOS = 5; // 表示する動画の数を動的に変更可能
    // 最新動画（created_at順にソート）
    const latestVideos = [...videos]
      .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
      .slice(0, NUM_LATEST_VIDEOS);
    updateLatestVideos(latestVideos);

    // カテゴリー別に分類して表示
    const categorizedVideos = categorizeVideos(videos);
    updateCategoryVideos(categorizedVideos);
  } catch (error) {
    console.error('エラーが発生しました:', error);
  }
};

// 動画リストをHTMLに更新する関数
function updateLatestVideos(videos) {
  const latestVideoList = document.getElementById('latestVideoList');
  latestVideoList.innerHTML = '';

  videos.forEach(video => {
    const listItem = document.createElement('li');
    listItem.className = 'video-item';
    listItem.innerHTML = `
      <strong class="video-title">${video.title}</strong>
      <iframe class="video-iframe" src="${video.url}" frameborder="0" allowfullscreen></iframe>
    `;
    latestVideoList.appendChild(listItem);
  });
}

// カテゴリーごとの動画を分類する関数
function categorizeVideos(videos) {
  const categorized = {};
  videos.forEach((video) => {
    const category = video.category || 'その他'; // カテゴリーが空の場合は「その他」
    if (!categorized[category]) {
      categorized[category] = [];
    }
    categorized[category].push(video);
  });

  return categorized;
}

// カテゴリー別セクションを動的に作成する関数
function updateCategoryVideos(categorizedVideos) {
  const categorySections = document.getElementById('categorySections');
  categorySections.innerHTML = '';

  for (const category in categorizedVideos) {
    const videoList = categorizedVideos[category];
    if (videoList.length === 0) continue; // 空のセクションはスキップ

    const section = document.createElement('section');
    section.className = 'video-section';
    const sanitizedCategory = sanitizeId(category);

    section.innerHTML = `
      <h2>${category}動画</h2>
      <ul id="categoryVideoList_${sanitizedCategory}" class="video-list"></ul>
    `;
    categorySections.appendChild(section);

    const videoListElement = section.querySelector('.video-list');
    videoList.forEach(video => {
      const listItem = document.createElement('li');
      listItem.className = 'video-item';
      listItem.innerHTML = `
        <strong class="video-title">${video.title}</strong>
        <iframe class="video-iframe" src="${video.url}" frameborder="0" allowfullscreen></iframe>
      `;
      videoListElement.appendChild(listItem);
    });
  }
}

// IDを安全な形式に変換する関数
function sanitizeId(category) {
  return category.replace(/\s+/g, '_').replace(/[^\w-]/g, '');
}
