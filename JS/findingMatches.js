// פונקציה לאימות הטופס
function validateForm() {
    var hobbies = document.getElementsByName('hobbies[]');
    var selectedHobbies = false;

    // בדיקה האם נבחר לפחות תחביב אחד
    for (var i = 0; i < hobbies.length; i++) {
        if (hobbies[i].checked) {
            selectedHobbies = true;
            break;
        }
    }

    // אם לא נבחרו תחביבים, הצג הודעת שגיאה  
    if (!selectedHobbies) {
        alert("יש לבחור לפחות תחביב אחד.");
        return false;
    }

    return true;
}

// הצגת והסתרת חלונית ההמתנה
function showWaiting() {
    document.getElementById('waitingContainer').style.display = 'block';
}

function hideWaiting() {
    document.getElementById('waitingContainer').style.display = 'none';
}

document.addEventListener("DOMContentLoaded", function () {
    var slider = document.getElementById("distance"); 
    var output = document.getElementById("distanceOutput"); // קבלת הטקסט שמציג את הערך

    // עדכון הערך של המרחק על פי התנועה של הסליידר
    output.innerHTML = slider.value + " ק\"מ "; // הצגת הערך הראשוני

    slider.oninput = function () {
        output.innerHTML = this.value + " ק\"מ "; // עדכון הערך בזמן אמת
    };

    document.querySelector('form').addEventListener('submit', function () {
        showWaiting(); // הצגת החלונית בהמתנה
    });
});


