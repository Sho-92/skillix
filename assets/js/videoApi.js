export async function fetchVideoApi() {
  const response = await fetch('../actions/videos/get_video.php');
  return response.json();
}

export async function addVideoApi(videoId) {
  const response = await fetch('../actions/videos/add_video.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(videoId),
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
  const response = await fetch('../actions/videos/delete_video.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id: videoId }),
  });
  return response.json();
}

export async function fetchSingleVideoApi(videoId) {
  const response = await fetch(`../actions/videos/get_single_video.php?id=${videoId}`);
  return response.json();
}
