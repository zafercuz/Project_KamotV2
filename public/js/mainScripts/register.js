$(document).ready(function () {
    const registerBtn = document.querySelector('#registerBtn'),
        branch = document.querySelector('#branch'),
        hrisid = document.querySelector('#hrisid'),
        name = document.querySelector('#name'),
        email = document.querySelector('#email'),
        password = document.querySelector('#password'),
        confirmPassword = document.querySelector('#password-confirm'),
        jsErrorMsg = document.querySelector('#jsErrorMsg'),
        exists = document.querySelector('#exists');

    if (branch.value != "") {
        hrisid.disabled = false;
        name.disabled = false;
        email.disabled = false;
        password.disabled = false;
        confirmPassword.disabled = false;
    }

    if (exists != null) {
        hrisid.style.borderColor = "red";
    }

    registerBtn.addEventListener('click', e => {
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

        hrisid.addEventListener('input', numbersOnly, true);
        name.addEventListener('input', lettersOnly, true);
    });

    const numbersOnly = () => {
        hrisid.value = hrisid.value.replace(/[^0-9.]/g, '').slice(0, 5).replace(/(\..*)\./g, '$1');
    };

    const lettersOnly = () => {
        name.value = name.value.replace(/[^a-z\s.]/gi, '').replace(/(\..*)\./g, '$1');
    };

});
