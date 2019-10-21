$(document).ready(function () {
  const currentpassword = document.querySelector('#current-password'),
    currentPassError = document.querySelector('#currentPassError'),
    newPassword = document.querySelector('#new-password'),
    newPasswordConfirm = document.querySelector('#new-password-confirm'),
    newPassConfirmError = document.querySelector('#newPassConfirmError'),
    newPassError = document.querySelector('#newPassError'),
    changePassBtn = document.querySelector('#changePassBtn');


    changePassBtn.addEventListener('click', e => {
      const rule = /^\S{8,}$/;

      /* CURRENT PASSWORD */
      if (currentpassword.value == ""){
        e.preventDefault();
        currentpassword.style.borderColor = "red"; 
        currentPassError.innerHTML = "This field must not be empty.";
      } else if (!currentpassword.value.match(rule)) {
        e.preventDefault();
        currentpassword.style.borderColor = "red"; 
        currentPassError.innerHTML = "This field must contain atleast 8 characters and must not have spaces.";
      } else {
        currentpassword.style.borderColor = ""; 
        currentPassError.innerHTML = "";
      }

      /* NEW PASSWORD */
      if (newPassword.value == ""){
        e.preventDefault();
        newPassword.style.borderColor = "red"; 
        newPassError.innerHTML = "This field must not be empty.";
      } else if (!newPassword.value.match(rule)) {
        e.preventDefault();
        newPassword.style.borderColor = "red"; 
        newPassError.innerHTML = "This field must contain atleast 8 characters and must not have spaces.";
      } else {
        newPassword.style.borderColor = ""; 
        newPassError.innerHTML = "";
      }

      /* CONFIRM NEW PASSWORD */
      if (newPassword.value != "" && newPassword.value.match(rule)){
        if (newPasswordConfirm.value == ""){
          e.preventDefault();
          newPasswordConfirm.style.borderColor = "red"; 
          newPassConfirmError.innerHTML = "This field must not be empty.";
        } else {
          newPasswordConfirm.style.borderColor = ""; 
          newPassConfirmError.innerHTML = "";
        }
      }
    });
});
