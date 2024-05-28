document.addEventListener("DOMContentLoaded", function () {
    var btnContinue = document.getElementById("btnContinue");
    var btnContinue2 = document.getElementById("btnContinue2");
    var btnSubmit = document.getElementById("btnSubmit");
    var part1 = document.getElementById("part1");
    var part2 = document.getElementById("part2");
    var part3 = document.getElementById("part3");
    var btnBack1 = document.getElementById("btnBack1");
    var btnBack2 = document.getElementById("btnBack2");

    btnContinue.addEventListener("click", function () {
        if (validatePart(part1)) {
            part1.style.display = "none";
            part2.style.display = "block";
        }
    });

    btnContinue2.addEventListener("click", function () {
        if (isAnyCheckboxChecked()) {
            part2.style.display = "none";
            part3.style.display = "block";
        }
    });

    btnBack1.addEventListener("click", function () {
        part2.style.display = "none";
        part1.style.display = "block";
    });

    btnBack2.addEventListener("click", function () {
        part3.style.display = "none";
        part2.style.display = "block";
    });

    function isAnyCheckboxChecked() {
        var checkboxes = document.querySelectorAll('#part2 input[type="checkbox"]:checked');
        if (checkboxes.length === 0) {
            alert("יש לבחור לפחות תחביב אחד.");
            return false;
        }
        return true;
    }

    btnSubmit.addEventListener("click", function () {
        validatePart(part3);
    });

    function validatePart(part) {
        var inputs = part.querySelectorAll("input[required], select[required]");
        for (var i = 0; i < inputs.length; i++) {
            if (!inputs[i].value.trim()) {
                alert("יש למלא את כל השדות הנדרשים.");
                return false;
            }
        }
        return true;
    }
});

document.addEventListener("DOMContentLoaded", function () {
    var profilePictureInput = document.getElementById("profilePictureInput");
    var profilePicturePreview = document.getElementById("profilePicturePreview");

    profilePictureInput.addEventListener("change", function () {
        if (profilePictureInput.files && profilePictureInput.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                profilePicturePreview.src = e.target.result;
            };

            reader.readAsDataURL(profilePictureInput.files[0]);
        }
    });
});

// Hebrew only validation
function enforceHebrewOnly(event) {
    var hebrewPattern = /^[\u0590-\u05FF\s]+$/;
    var input = event.target;
    if (!hebrewPattern.test(input.value)) {
        input.value = input.value.replace(/[^\u0590-\u05FF\s]/g, '');
    }
}

// English only validation
function enforceAlphanumeric(event) {
    var alphanumericPattern = /^[a-zA-Z0-9\s]+$/;
    var input = event.target;
    if (!alphanumericPattern.test(input.value)) {
        input.value = input.value.replace(/[^a-zA-Z0-9\s]/g, '');
    }
}

var firstNameInput = document.getElementById("firstName");
var lastNameInput = document.getElementById("lastName");
var usernameInput = document.getElementById("username");
var passwordInput = document.getElementById("password");

firstNameInput.addEventListener("input", enforceHebrewOnly);
lastNameInput.addEventListener("input", enforceHebrewOnly);
usernameInput.addEventListener("input", enforceAlphanumeric);
passwordInput.addEventListener("input", enforceAlphanumeric);
