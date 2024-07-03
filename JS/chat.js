const form = document.querySelector(".typing-area"),
    incoming_id = form.querySelector(".incoming_id").value,
    inputField = form.querySelector(".input-field"),
    sendBtn = form.querySelector("button"),
    chatBox = document.querySelector(".chat-box");

let isScrollingUp = false;

form.onsubmit = (e) => {
    e.preventDefault();
    sendMessage();
};

//הצגת ההודעות שהמשתמש המחובר שולח באופן מיידי
function sendMessage() {
    let formData = new FormData(form);
    formData.append('action', 'send_message');

    fetch("chat.php?receiver=" + incoming_id, {
        method: 'POST',
        body: formData
    }).then(response => response.text()).then(data => {
        if (data === "success") {
            inputField.value = "";
            scrollToBottom();
        } else {
            console.error("Failed to send message: " + data);
        }
    }).catch(error => console.error('Error:', error));
}

//מונע גלילה אוטומטית לתחתית הצ'אט בעת גלילה כלפי מעלה שמבצע משתמש
chatBox.onscroll = () => {
    if (chatBox.scrollTop + chatBox.clientHeight < chatBox.scrollHeight) {
        isScrollingUp = true;
    } else {
        isScrollingUp = false;
    }
};

//קריאה לפונקציה fetchMessages כל 50 מילי-שניות
setInterval(() => {
    fetchMessages();
}, 50);

//הצגת ההודעות שמתקבלות באופן מיידי
function fetchMessages() {
    let formData = new FormData();
    formData.append('action', 'get_messages');

    fetch("chat.php?receiver=" + incoming_id, {
        method: 'POST',
        body: formData
    }).then(response => response.text()).then(data => {
        chatBox.innerHTML = data;
        if (!isScrollingUp) {
            scrollToBottom();
        }
    }).catch(error => console.error('Error:', error));
}

function scrollToBottom() {
    chatBox.scrollTop = chatBox.scrollHeight;
}
