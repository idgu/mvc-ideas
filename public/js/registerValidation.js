function emailExists(emailInput) {
    const emailValue = emailInput.value;
    const emailError = document.querySelector('#emailError');

    var xhr = new XMLHttpRequest();
    xhr.open('GET', '../account/xhr-validate-email?email=' + emailValue, true);

    xhr.onload = function() {
        if (this.status == 200) {
            if(JSON.parse(this.responseText)) {
                emailError.innerHTML = 'Email already exists in database!';
            } else {
                emailError.innerHTML = '';
            }
        }


    }
    xhr.send();
}

document.addEventListener('DOMContentLoaded', function () {
    const emailInput = document.querySelector('#inputEmail');


    emailInput.addEventListener('change', function () {
        emailExists(emailInput);
    })
});