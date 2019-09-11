console.log("DTR!"); // JUST TO MAKE SURE JS IS RUNNING

//BUTTON
const Searchbtn = document.querySelector('#btnSearch');

//ERROR MSGS VARIABLES
const errorContainer = document.querySelector('.errorMsgs');

//TABLE VARIABLES
const chooseFilter = document.querySelector('#chooseFilter'),
  filterInput = document.querySelector('#filterInput'),
  filterBranch = document.querySelector('#filterBranch'),
  date1 = document.querySelector('#date1'),
  date2 = document.querySelector('#date2'),
  chooseSort = document.querySelector('#chooseSort'),
  tableBodyResult = document.querySelector('#tableBodyResult'),
  hrisIdPopup = document.querySelector('#hrisIdPopup'),
  ascendingdescending = document.querySelector('#ascendingdescending'),
  noData = document.querySelector('#noData');

// EVENT LISTENERS
chooseFilter.addEventListener('change', e => {
  let chooseFilterValue = chooseFilter.options[chooseFilter.selectedIndex].value;
  //IF BRANCH IS SELECTED, DISABLE INPUT FIELD
  if (chooseFilterValue === "2") {
    filterInput.placeholder = "";
    filterInput.disabled = true;
  }
  else {
    if (chooseFilterValue === "1") {
      filterInput.placeholder = "Input HRIS ID";
    } else if (chooseFilterValue === "3") {
      filterInput.placeholder = "Input Employee Name";
    }
    filterInput.disabled = false;
  }
});

Searchbtn.addEventListener('click', e => {
  let errors = [],
    messageHTML = "",
    todayDate = moment().format('YYYY-MM-D'),
    chooseFilterValue = chooseFilter.options[chooseFilter.selectedIndex].value;
  e.preventDefault();

  // VALIDATE FILTER INPUTS
  const validate = formValidate(errors, chooseFilterValue, todayDate);
  if (!validate) {
    console.log("ERRORS!!!! FIX");
    errorContainer.style.display = 'block';
    displayError(errors, messageHTML);
  } else {
    hrisIdPopup.style.display = 'block';
    console.log("Error free");
    errorContainer.querySelector("ul").innerHTML = messageHTML;
    errorContainer.style.display = 'none';
    // AJAX REQUEST HERE
  }

});

const displayError = (errors, messageHTML) => {
  errors.forEach(error => {
    messageHTML += "<li>" + error + "</li>";
  });
  errorContainer.querySelector("ul").innerHTML = messageHTML;
};

//FORM VALIDATION (FOR CHECKING ERRORS)
const formValidate = (errors, filterValue, todayDate) => {
  //LOCAL VARIABLES FOR VALIDATION
  const letters = /^[a-zA-Z\s]*$/;
  const numbers = /^[0-9]+$/;
  const numberRange = /^\d{5}$/;
  let errorCount = 0;

  //SETTING DEFUALT STYLES FOR INPUT FIELDS
  chooseFilter.style.borderColor = "";
  filterInput.style.borderColor = "";
  filterBranch.style.borderColor = "";
  date1.style.borderColor = "";
  date2.style.borderColor = "";

  // CHECK IF REQUIRED FIELDS ARE EMPTY
  if (filterValue === "" && filterInput.value === "" && filterBranch.value === "" && date1.value === "" && date2.value === "") {
    chooseFilter.style.borderColor = "red";
    filterBranch.style.borderColor = "red";
    date1.style.borderColor = "red";
    date2.style.borderColor = "red";
    console.log("Fields must not be empty!");
    errors.push("Fields must not be empty!");
    return false;
  }

  // CHECKING FOR 1ST FILTER
  if (filterValue === "") {
    chooseFilter.style.borderColor = "red";
    errorCount++;
    errors.push("You must choose a filter!");
  }

  if (filterValue !== "" && filterInput.value === "" && filterValue !== "2") {
    filterInput.style.borderColor = "red";
    errorCount++;
    errors.push("Filter input must not be empty!");
  }

  if (filterValue === "1" && filterInput.value !== "") {
    if (!filterInput.value.match(numberRange)) {
      filterInput.style.borderColor = "red";
      errorCount++;
      errors.push("Filter input must be only numeric character and exactly 5 digits!");
    }
  }

  // CHECKING FOR BRANCH FILTER INPUT
  if (filterBranch.value === "") {
    filterBranch.style.borderColor = "red";
    errorCount++;
    errors.push("Branch input must not be empty!");
  }

  // CHECKING FOR DATE FILTER INPUTS
  if (date1.value === "") {
    date1.style.borderColor = "red";
    errorCount++;
    errors.push("Date 1 must not be empty!");
  }

  if (date2.value === "") {
    date2.style.borderColor = "red";
    errorCount++;
    errors.push("Date 2 must not be empty!");
  }

  if (date1.value > date2.value) {
    date1.style.borderColor = "red";
    errorCount++;
    errors.push("Date 1 must not be greater than Date 2!");
  }

  if (date2.value < date1.value) {
    date2.style.borderColor = "red";
    errorCount++;
    errors.push("Date 2 must not be less than Date 1!");
  }

  if (date1.value > todayDate) {
    date1.style.borderColor = "red";
    errorCount++;
    errors.push("Date 1 must not greater than current date!");
  }

  if (date2.value > todayDate) {
    date2.style.borderColor = "red";
    errorCount++;
    errors.push("Date 2 must not greater than current date!");
  }

  ///////////////////// END OF VALIDATION /////////////////////

  if (errorCount === 0) {
    return true; // RETURN TRUE IF NO ERROR
  } else {
    return false; // RETURN FALSE IF THERES AN ERROR
  }

};