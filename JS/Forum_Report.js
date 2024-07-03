document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("status").addEventListener("change", function () {
        this.form.submit();
    });
});