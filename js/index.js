function addVideo() {
  const title = document.getElementById('videoTitle').value;
  const url = document.getElementById('videoUrl').value;
  if (!title || !url) {
    alert("Both Title and URL are required!");
    return;
  }

  const videoId = url.split('/').pop().replace("watch?v=", "");
  const embedUrl = `https://www.youtube.com/embed/${videoId}`;

  const adminList = document.getElementById('adminVideoList');
  const listItem = document.createElement('li');
  listItem.innerHTML = `<strong>${title}</strong><br>
    <iframe width="300" height="170" src="${embedUrl}" frameborder="0" allowfullscreen></iframe><br>
    <strong>${embedUrl}</strong><br>
    <button class="delete-btn">削除</button>`;
  adminList.appendChild(listItem);

  const videoData = JSON.parse(localStorage.getItem('videos')) || [];
  videoData.push({ title, embedUrl });
  localStorage.setItem('videos', JSON.stringify(videoData));
  document.getElementById('videoTitle').value = '';
  document.getElementById('videoUrl').value = '';
};

function loadVideos() {
  const videoData = JSON.parse(localStorage.getItem('videos')) || [];
  const adminList = document.getElementById('adminVideoList');
  adminList.innerHTML = '';

  videoData.forEach(video => {
    const listItem = document.createElement('li');
    listItem.innerHTML = `<strong>${video.title}</strong><br>
      <iframe width="300" height="170" src="${video.embedUrl}" frameborder="0" allowfullscreen></iframe><br>
      <strong>${video.embedUrl}</strong><br>
      <button class="delete-btn">削除</button>`;
    adminList.appendChild(listItem);
  });

  const deleteButtons = document.querySelectorAll('.delete-btn');
  deleteButtons.forEach(button => {
    button.addEventListener('click', function() {
      const index = button.getAttribute('data-index'); 
      const confirmDelete = window.confirm("本当にこの動画を削除しますか？");
      if (confirmDelete) {
        deleteVideo(index);  
      }
    });
  });
};

function deleteVideo(index) {
  const videoData = JSON.parse(localStorage.getItem('videos')) || [];

  videoData.splice(index, 1);
  localStorage.setItem('videos', JSON.stringify(videoData));

  loadVideos();
};

window.onload = loadVideos;
