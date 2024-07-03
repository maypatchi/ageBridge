function deletePost(postId) {
    if (confirm('האם אתה בטוח שברצונך למחוק את הפוסט?')) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "delete_post.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                alert(xhr.responseText);
                location.reload();
            }
        };
        xhr.send("post_id=" + postId);
    }
}