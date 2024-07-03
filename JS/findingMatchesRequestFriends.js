function sendFriendRequest(button, receiverUsername) {
    $.ajax({
        type: 'POST',
        url: 'sendFriendRequest.php',
        data: { receiver_username: receiverUsername, sendRequest: true },
        success: function (response) {
            if (response == 'success') {
                $(button).css('background-color', 'red');
                $(button).text('בטל בקשה');
                $(button).attr('onclick', 'cancelFriendRequest(this, \'' + receiverUsername + '\')');
            } else {
                alert('Error sending friend request');
            }
        }
    });
}

function cancelFriendRequest(button, receiverUsername) {
    $.ajax({
        type: 'POST',
        url: 'cancelFriendRequest.php',
        data: { receiver_username: receiverUsername, cancelRequest: true },
        success: function (response) {
            if (response == 'success') {
                $(button).css('background-color', 'blue');
                $(button).text('שלח בקשה');
                $(button).attr('onclick', 'sendFriendRequest(this, \'' + receiverUsername + '\')');
            } else {
                alert('Error cancelling friend request');
            }
        }
    });
}
