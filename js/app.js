// $(document).ready(function () {
// 	$('#resultTable').DataTable();
// 	// $('.dataTables_length').addClass('bs-select');
// });
console.log("DTR!");

//BUTTON
const Searchbtn = document.querySelector('#btnSearch');

//TABLE VARIABLES
const chooseFilter = document.querySelector('#chooseFilter'),
  filterInput = document.querySelector('#filterInput'),
  filterBranch = document.querySelector('#filterBranch'),
  date1 = document.querySelector('#date1'),
  date2 = document.querySelector('#date2'),
  logType = document.querySelector('#logType');
  logTypeIn = document.querySelector('#logTypeIn'),
  logTypeOut = document.querySelector('#logTypeOut'),
  chooseSort = document.querySelector('#chooseSort'),
  tableBodyResult = document.querySelector('#tableBodyResult'),
  hrisIdPopup = document.querySelector('#hrisIdPopup'),
  form = document.querySelector('.filterForm'),
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
  let text = "",
    chooseFilterValue = chooseFilter.options[chooseFilter.selectedIndex].value;
  e.preventDefault();
  

  // VALIDATE FILTER INPUTS
  let validate = formValidate(chooseFilterValue);
  if(validate){
    hrisIdPopup.style.display = 'block';
    console.log("Error free");
  } else {
    console.log("ERRORS!!!! FIX");
  }
  

  // updateResult();
});


//FORM VALIDATION (FOR CHECKING ERRORS)
const formValidate = (filterValue) => {
  //LOCAL VARIABLES FOR VALIDATION
  const letters = /^[a-zA-Z\s]*$/;;
  const numbers = /^[0-9]+$/;
  const mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
  const numberRange = /^\d{11}$/;
  let errorCount = 0;

  //SETTING DEFUALT STYLES FOR INPUT FIELDS
  chooseFilter.style.borderColor = "";
  filterInput.style.borderColor = "";
  filterBranch.style.borderColor = "";
  date1.style.borderColor = "";
  date2.style.borderColor = "";
  logType.style.backgroundColor = "";

  // CHECK IF REQUIRED FIELDS ARE EMPTY
  if (filterValue === "" && filterInput.value === "" && filterBranch.value === "" && date1.value === "" && date2.value === ""
    && (!logTypeIn.checked && !logTypeOut.checked)) {
    chooseFilter.style.borderColor = "red";
    filterBranch.style.borderColor = "red";
    date1.style.borderColor = "red";
    date2.style.borderColor = "red";
    logType.style.backgroundColor = "red";
    console.log("Fields must not be empty!");
    return false;
  }

  if (filterValue === "") {
    chooseFilter.style.borderColor = "red";
    errorCount++;
    console.log("You must choose a filter!");
  }

  if (filterValue !== "" && filterInput.value === "" && filterValue !== "2") {
    filterInput.style.borderColor = "red";
    errorCount++;
    console.log("Filter input must not be empty!");
  }

  if (filterBranch.value === ""){
    filterBranch.style.borderColor = "red";
    errorCount++;
    console.log("Branch input must not be empty!");
  }

  if(date1.value === ""){
    date1.style.borderColor = "red";
    errorCount++;
    console.log("Date 1 must not be empty!");
  }

  if(date2.value === ""){
    date2.style.borderColor = "red";
    errorCount++;
    console.log("Date 2 must not be empty!");
  }

  if(!logTypeIn.checked && !logTypeOut.checked){
    logType.style.backgroundColor = "red";
    errorCount++;
    console.log("Log Type must at least have 1 checked!");
  }

  if(errorCount === 0){
    return true; // RETURN TRUE IF NO ERROR
  } else {
    return false; // RETURN FALSE IF THERES AN ERROR
  }

};

//FOR MANIPULATING DOM
// const updateResult = () => {
//   console.log("Update");
//   text += '<tr><td>2019-09-03 (7:56:00 AM)</td><td>IN</td><td>Steven</td></tr><tr><td>2019-09-03 (6:09:02 PM)</td><td>OUT</td><td>Steven</td></tr><tr><td>2019-09-04 (7:40:28 AM)</td><td>IN</td><td>Steven</td></tr><tr><td>2019-09-04 (6:13:56 PM)</td><td>OUT</td><td>Steven</td></tr>';
//   tableBodyResult.querySelector('tbody').innerHTML = text;
// };