// $(document).ready(function () {
// 	$('#resultTable').DataTable();
// 	// $('.dataTables_length').addClass('bs-select');
// });
console.log("DTR!");

//GLOBAL VARIABLES
let text = '';

//BUTTON
const Searchbtn = document.querySelector('#btnSearch');

//TABLE VARIABLES
let chooseFilter = document.querySelector('#chooseFilter');

let filterInput = document.querySelector('#filterInput');
let filterBranch = document.querySelector('#filterBranch');
let date1 = document.querySelector('#date1');
let date2 = document.querySelector('#date2');logTypeIn
let logTypeIn = document.querySelector('#logTypeIn');
let logTypeOut = document.querySelector('#logTypeOut');
let chooseSort = document.querySelector('#chooseSort');

let hrisIdPopup = document.querySelector('#hrisIdPopup');
let tableBodyResult = document.querySelector('#tableBodyResult');
const form = document.querySelector('.filterForm');
const ascendingdescending = document.querySelector('#ascendingdescending');
const noData = document.querySelector('#noData');

//FILTER TABLE (TEST)

// EVENT LISTENERS
chooseFilter.addEventListener('change', e => {
  let chooseFilterValue = chooseFilter.options[chooseFilter.selectedIndex].value;
  // ascendingdescending.disabled = false;
  if (chooseFilterValue === "2") {
    filterInput.disabled = true;
  }
  else {
    filterInput.disabled = false;
  }

});

Searchbtn.addEventListener('click', e => {
  let text = '';
  e.preventDefault();
  hrisIdPopup.style.display = 'block';

  // VALIDATES FILTER INPUTS
  formValidate();

  // updateResult();
});

const formValidate = () => {
  console.log("Validation");
};

//FOR MANIPULATING DOM
const updateResult = () => {
  console.log("Update");
  text += '<tr><td>2019-09-03 (7:56:00 AM)</td><td>IN</td><td>Steven</td></tr><tr><td>2019-09-03 (6:09:02 PM)</td><td>OUT</td><td>Steven</td></tr><tr><td>2019-09-04 (7:40:28 AM)</td><td>IN</td><td>Steven</td></tr><tr><td>2019-09-04 (6:13:56 PM)</td><td>OUT</td><td>Steven</td></tr>';
  tableBodyResult.querySelector('tbody').innerHTML = text;
};