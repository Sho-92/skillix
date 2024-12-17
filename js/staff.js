const videoData = JSON.parse(localStorage.getItem('videos')) || [];
    const staffList = document.getElementById('staffVideoList');

    videoData.forEach(video => {
      const listItem = document.createElement('li');
      listItem.innerHTML = `<strong>${video.title}</strong><br>
        <iframe width="300" height="170" src="${video.embedUrl}" frameborder="0" allowfullscreen></iframe>`;
      staffList.appendChild(listItem);
    });