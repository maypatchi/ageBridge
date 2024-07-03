// הסרת חבר מרשימת חברים
function removeFriend(username) {
    if (confirm("האם אתה בטוח שברצונך להסיר חבר זה?")) {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "remove_friend.php?username=" + encodeURIComponent(username), true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                alert("החבר נמחק בהצלחה.");
                const friendCard = document.getElementById('friend-' + username);
                friendCard.parentNode.removeChild(friendCard);
            }
        };
        xhr.send();
    }
}