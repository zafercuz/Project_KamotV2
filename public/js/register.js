$(document).ready(function () {
    const registerBtn = document.querySelector('#registerBtn'),
        branch = document.querySelector('#branch'),
        hrisid = document.querySelector('#hrisid'),
        name = document.querySelector('#name'),
        email = document.querySelector('#email'),
        password = document.querySelector('#password'),
        confirmPassword = document.querySelector('#password-confirm'),
        jsErrorMsg = document.querySelector('#jsErrorMsg');

    registerBtn.addEventListener('click', e => {
        jsErrorMsg.innerHTML = "You must select a branch.";
        if (branch.value == "") {
            e.preventDefault();
            branch.style.borderColor = "red";
            jsErrorMsg.innerHTML = "You must select a branch.";
        } else {
            branch.style.borderColor = "";
            jsErrorMsg.innerHTML = "";
        }
    });

    branch.addEventListener('change', e => {
        e.preventDefault();
        branch.style.borderColor = "";
        jsErrorMsg.innerHTML = "";
        hrisid.disabled = false;
        name.disabled = false;
        email.disabled = false;
        password.disabled = false;
        confirmPassword.disabled = false;
    });

});
