function validateForm() {
    var hobbies = document.getElementsByName('hobbies[]');
    var selectedHobbies = false;

    for (var i = 0; i < hobbies.length; i++) {
        if (hobbies[i].checked) {
            selectedHobbies = true;
            break;
        }
    }

    if (!selectedHobbies) {
        alert("יש לבחור לפחות תחביב אחד.");
        return false; 
    }
    return true; 
}

document.addEventListener("DOMContentLoaded", function () {
    var slider = document.getElementById("distance");
    var output = document.getElementById("distanceOutput");
    output.innerHTML = slider.value + " ק\"מ ";

    slider.oninput = function () {
        output.innerHTML = this.value + " ק\"מ ";
    };
});


