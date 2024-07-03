document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var lastClickedDate = null;

    //יצירת היומן והגדרת מאפיינים
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'he',
        headerToolbar: {
            left: 'prev,next today',
            right: 'title'
        },
        //טעינת הפגישות הקיימות 
        events: 'loadMeetings.php',

        dateClick: function (info) {
            if (lastClickedDate) {
                lastClickedDate.dayEl.style.backgroundColor = ''; 
            }
            lastClickedDate = info;
            document.getElementById('date').value = info.dateStr;
            info.dayEl.style.backgroundColor = 'white';

            //פתיחת חלונית לקביעת פגישה חדשה
            var meetingModal = new bootstrap.Modal(document.getElementById('meetingModal'));
            meetingModal.show();
        },

        //הצגת פרטי פגישה קיימת בעת לחיצה על הפגישה
        eventClick: function (info) {
            var eventDetails = info.event.extendedProps;
            var timeWithoutSeconds = eventDetails.time.split(':').slice(0, 2).join(':');


            var modalContent = `
            שעה: ${timeWithoutSeconds}
            סוג פגישה: ${eventDetails.communication}
            מיקום: ${eventDetails.location}
            עם מי הפגישה: ${eventDetails.user}
            `;

            alert(modalContent);
        }
    });

    calendar.render();

    //שליחת פרטי הפגישה החדשה שהוזנה לשרת 
    document.getElementById('meetingForm').addEventListener('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);

        fetch('savingMeeting.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    calendar.refetchEvents();
                    document.getElementById('meetingForm').reset();
                    var meetingModal = bootstrap.Modal.getInstance(document.getElementById('meetingModal'));
                    meetingModal.hide(); 
                } else {
                    console.error('Error:', data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    });
});


