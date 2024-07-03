document.querySelectorAll('.new-message-notification').forEach(notification => {
    notification.addEventListener('click', function (event) {
        var messageId = this.getAttribute('data-id');

        // שליחת בקשה לשרת לעדכן את סטטוס ההודעה
        fetch('updateMessageStatus.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'message_id=' + messageId
        })
            .then(response => response.text())
            .then(data => {
                console.log(data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
});

