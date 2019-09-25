$(document).ready(function () {
  console.log("DTR!"); // JUST TO MAKE SURE JS IS RUNNING

  //BUTTON
  const Searchbtn = document.querySelector('#btnSearch');

  //ERROR MSGS VARIABLES
  const errorMsgs = document.querySelector('.errorMsgs');

  //TABLE VARIABLES
  const
    table = $('#resultTable').DataTable({
      pagingType: 'full_numbers',
    }),
    chooseFilter = document.querySelector('#chooseFilter'),
    filterInput = document.querySelector('#filterInput'),
    filterBranch = document.querySelector('#filterBranch'),
    date1 = document.querySelector('#date1'),
    date2 = document.querySelector('#date2'),
    hrisIdPopup = document.querySelector('#hrisIdPopup'),
    hrisID = document.querySelector('#hrisID');

  // EVENT LISTENERS
  chooseFilter.addEventListener('change', e => {
    let chooseFilterValue = chooseFilter.options[chooseFilter.selectedIndex].value;
    filterInput.value = "";
    //IF BRANCH IS SELECTED, DISABLE INPUT FIELD
    if (chooseFilterValue === "2") {
      filterInput.placeholder = "";
      filterInput.disabled = true;
    }
    else {
      if (chooseFilterValue === "1") {
        filterInput.placeholder = "Input HRIS ID";
        filterInput.removeEventListener("input", lettersOnly, true);
        filterInput.addEventListener('input', numbersOnly, true);
      }
      else if (chooseFilterValue === "3") {
        filterInput.placeholder = "Input Employee Name";
        filterInput.removeEventListener("input", numbersOnly, true);
        filterInput.addEventListener('input', lettersOnly, true);
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

    if (!validate) { // THERE ARE ERRORS
      errorMsgs.style.display = 'block';
      displayError(errors, messageHTML);
    }
    else { // ERROR FREE
      displayHrisId(chooseFilterValue);
      errorMsgs.querySelector("ul").innerHTML = messageHTML; // CLEAR THE ERROR LIST
      errorMsgs.style.display = 'none';

      table.clear().draw();
      // AJAX REQUEST CALL FUNCTION HERE
      $.ajax({
        type: 'get',
        url: '/SearchAjax',
        _token: "{{ csrf_token() }}",
        data: {
          'chooseFilterValue': chooseFilterValue,
          'filterInput': filterInput.value,
          'filterBranch': filterBranch.value,
          'date1': date1.value,
          'date2': date2.value,
        },
        success: function (data) {
            console.log(data);
            transformTable(data);
        },
        error: function (error) {
            console.log(error);
            errorMsgs.querySelector("ul").innerHTML = "<li>No data found</li>"; // CLEAR THE ERROR LIST
            errorMsgs.style.display = 'block';
        }
      });
      console.log("AJAX");
    }
  });


  //////////////////////////////////////////////////////////////////////////////////////
  // FUNCTIONS FUNCTIONS FUNCTIONS FUNCTIONS FUNCTIONS FUNCTIONS FUNCTIONS FUNCTIONS //
  ////////////////////////////////////////////////////////////////////////////////////

  /**
   * For restriction on input field
   *
   */
  const numbersOnly = () => {
    filterInput.value = filterInput.value.replace(/[^0-9.]/g, '').slice(0, 5).replace(/(\..*)\./g, '$1');
  };

  const lettersOnly = () => {
    filterInput.value = filterInput.value.replace(/[^a-z\s.]/gi, '').replace(/(\..*)\./g, '$1');
  };

  /**
   *
   * Displays list of errors
   * @param {*} errors
   * @param {*} messageHTML
   */
  const displayError = (errors, messageHTML) => {
    errors.forEach(error => {
      messageHTML += "<li>" + error + "</li>";
    });
    errorMsgs.querySelector("ul").innerHTML = messageHTML;
  };


  /**
   *
   * Displays HRIS ID
   * @param {*} chooseFilterValue
   */
  const displayHrisId = chooseFilterValue => {
    if (chooseFilterValue === "1") {
      hrisID.innerHTML = filterInput.value;
      hrisIdPopup.style.display = 'block';
    }
    else {
      hrisID.value = "";
      hrisIdPopup.style.display = 'none';
    }
  };

  //FORM VALIDATION (FOR CHECKING ERRORS)
  const formValidate = (errors, filterValue, todayDate) => {
    //LOCAL VARIABLES FOR VALIDATION
    const letters = /^[a-zA-Z\s]*$/;
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
      errors.push("Fields must not be empty!");
      return false;
    }

    // CHECKING FOR 1ST FILTER
    if (filterValue === "") {
      errorStyleMsg(chooseFilter, errors, "You must choose a filter!");
      errorCount++;
    }

    if (filterValue !== "" && filterInput.value === "" && filterValue !== "2") {
      errorStyleMsg(filterInput, errors, "Filter input must not be empty!");
      errorCount++;
    }

    if (filterValue === "1" && filterInput.value !== "") {
      if (!filterInput.value.match(numberRange)) {
        errorStyleMsg(filterInput, errors, "Filter input must be exactly 5 digits!");
        errorCount++;
      }
    }

    if (filterValue === "3" && filterInput.value !== "") {
      if (!filterInput.value.match(letters)) {
        errorStyleMsg(filterInput, errors, "Filter input must consist of only alphabet characters!");
        errorCount++;
      }
      else if (!filterInput.value.match(/[a-z]/i)) {
        errorStyleMsg(filterInput, errors, "Filter input must not be empty!");
        errorCount++;
      }
    }

    // CHECKING FOR BRANCH FILTER INPUT
    if (filterBranch.value === "") {
      errorStyleMsg(filterBranch, errors, "Branch input must not be empty!");
      errorCount++;
    }

    // CHECKING FOR DATE FILTER INPUTS
    if (date1.value === "") {
      errorStyleMsg(date1, errors, "Date 1 must not be empty!");
      errorCount++;
    }

    if (date2.value === "") {
      errorStyleMsg(date2, errors, "Date 2 must not be empty!");
      errorCount++;
    }

    if (date1.value > date2.value && date2.value !== "") {
      errorStyleMsg(date1, errors, "Date 1 must not be greater than Date 2!");
      errorCount++;
    }

    if (date2.value < date1.value && date1.value !== "") {
      errorStyleMsg(date2, errors, "Date 2 must not be less than Date 1!");
      errorCount++;
    }

    if (date1.value > todayDate) {
      errorStyleMsg(date1, errors, "Date 1 must not be greater than current date!");
      errorCount++;
    }

    if (date2.value > todayDate) {
      errorStyleMsg(date2, errors, "Date 2 must not be greater than current date!");
      errorCount++;
    }

    ///////////////////// END OF VALIDATION /////////////////////

    if (errorCount === 0) {
      return true; // RETURN TRUE IF NO ERROR
    }
    else {
      return false; // RETURN FALSE IF THERES AN ERROR
    }

  };

  // FUNCTION FOR ERROR MESSAGE AND STYLE
  const errorStyleMsg = (styleArr, errorArr, errorMsg) => {
    if (Array.isArray(styleArr) == true) {
      styleArr.forEach(element => {
        element.style.color = "red";
      });
    }
    else {
      styleArr.style.borderColor = "red";
    }
    errorArr.push(errorMsg);
  };

  /**
   * For DOM Manipulation on result table
   *
   */
  const transformTable = data => {
    let collection = data.collection;
    console.log(collection);

    collection.forEach(element => {
      table.row.add([
        element.date,
        element.logIn,
        element.logOut,
        element.name,
      ]).draw();
    });
  };

});