$(document).ready(function () {
    // פונקציה שמציגה את החלון הקופץ להוספת פוסט חדש
    $("#addPostBtn").click(function () {
        $("#addPostModal").modal("show");
    });

    // פונקציה שמזיזה את הכפתור כאשר מגיעים לפוטר
    function adjustButtonPosition() {
        var footerHeight = $('footer').outerHeight();
        var footerTop = $('footer').offset().top;
        var windowBottom = $(window).scrollTop() + $(window).height();

        if (windowBottom > footerTop) {
            $('#addPostBtn').css('bottom', (windowBottom - footerTop) + 20 + 'px');
        } else {
            $('#addPostBtn').css('bottom', '20px');
        }
    }

    // קריאה לפונקציה כאשר הדף נטען וכאשר גוללים את הדף
    $(window).on('load scroll resize', adjustButtonPosition);

    // טיפול בלחצן לייק
    document.querySelectorAll('.like-button').forEach(function (button) {
        button.addEventListener('click', function () {
            var post_id = this.closest('.card-body').querySelector('input[name="post_id"]').value;
            var likesCountElement = this.closest('.card-body').querySelector('.likes-count');

            fetch('like_post.php', {
                method: 'POST',
                body: JSON.stringify({
                    like_button: true,
                    post_id: post_id
                }),
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        likesCountElement.textContent = data.likes_count;
                        if (data.liked) {
                            button.textContent = 'Unlike';
                            button.classList.add('unlike-button');
                        } else {
                            button.textContent = 'Like';
                            button.classList.remove('unlike-button');
                        }
                    } else {
                        console.error('Error:', data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });

    // בעת לחיצה על כפתור הוספת תגובה, נפתחת התיבה להקלדת התגובה 
    document.querySelectorAll('.comment-button').forEach(function (button) {
        button.addEventListener('click', function () {
            var postId = button.getAttribute('data-post-id');
            var commentForm = document.querySelector('.add-comment-form[data-post-id="' + postId + '"]');
            commentForm.style.display = 'block';
            button.style.display = 'none';
        });
    });

    //הוספת תגובות לפוסטים והצגתן בפורום באופן מיידי
    document.querySelectorAll('.add-comment-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            var postId = form.querySelector('input[name="postId"]').value;
            var commentContent = form.querySelector('textarea[name="commentContent"]').value;

            fetch('upload_comment.php', {
                method: 'POST',
                body: JSON.stringify({
                    postId: postId,
                    commentContent: commentContent
                }),
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); 
                    } else {
                        console.error('Error:', data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });
});

// פונקציה לפתיחת חלונית הדיווח
$('.report-button').click(function () {
    var postId = $(this).attr('data-post-id');
    $('#reportModal').modal('show');
    $('#reportModal').data('post-id', postId); 
});

// שליחת טופס הדיווח
$('#reportForm').submit(function (event) {
    event.preventDefault();
    var postId = $('#reportModal').data('post-id');
    var reportReason = $('input[name="reason"]:checked').val(); 

    fetch('report_post.php', {
        method: 'POST',
        body: JSON.stringify({
            postId: postId,
            reason: reportReason
        }),
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("הדיווח נשמר בהצלחה");
                $('#reportModal').modal('hide'); 
            } else {
                console.error('Error:', data.message);
            }
        })
        .catch(error => console.error('Error:', error));
});
