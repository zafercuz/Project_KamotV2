@extends('layouts.app')

@section('content')

<div class="container mt-4 mb-5">
  <!-- Error Message section -->
  <div class="container errorMsgs">
    <h5>Error List</h5>
    <ul class="errorList">

    </ul>
  </div>
  <!-- FILTER TABLE SECTION -->
  <form class="form-inline filterForm">
    <div class="container py-2" id="filterTable">
      <!-- ROW -->
      <div class="form-group row">
        <!-- ANOTHER COLUMN -->
        <div class="col mb-2">
          <div class="d-sm-flex align-items-baseline">
            <span class="col-label mr-2">Filter:</span>
            <select class="form-control custom-select mr-sm-2 custom-select-sm" id="chooseFilter">
              <option selected="" disabled="" value="">Choose Filter</option>
              <option value="1">HRIS ID</option>
              <option value="2">BRANCH</option>
              <option value="3">EMPLOYEE NAME</option>
            </select>
            <input type="text" class="form-control form-control-sm" id="filterInput" placeholder="" disabled>
          </div>
        </div>
        <!-- ANOTHER COLUMN -->
        <div class="col mb-2">
          <div class="d-sm-flex align-items-baseline">
            <span class="col-label mr-2">Branch:</span>
            <select class="form-control custom-select mr-sm-2 custom-select-sm" id="filterBranch">
              <option selected="" disabled="" value="">Choose Branch</option>
              <option value="000">000 - Head Office</option>
              <option value="074">074 - Mandaue HMWI</option>
            </select>
          </div>
        </div>
      </div>

      <!-- ROW -->
      <div class="form-group row mb-2">
        <!-- ANOTHER COLUMN -->
        <div class="col">
          <div class="d-sm-flex align-items-baseline">
            <span class="col-label mr-2">Date Range:</span>
            <input type="date" class="form-control form-control-sm" id="date1">
            <i class="fa fa-calendar mx-1"></i>
            <input type="date" class="form-control form-control-sm mr-sm-2" id="date2">
          </div>
        </div>
      </div>

      <!-- ROW -->
      <div class="form-group row">
        <div class="col text-center">
          <div class="d-sm-flex align-items-baseline justify-content-sm-center">
            <button type="submit" class="btn btn-info" id="btnSearch"><i class="fa fa-search"></i> SEARCH</button>
          </div>
        </div>
      </div>

      <!-- ROW -->
      <div class="form-group row">
        <!-- ANOTHER COLUMN -->
        <div class="col-12">
          <span id="hrisIdPopup">HRIS ID: <span id="hrisID"></span></span>
        </div>
      </div>
    </div>
  </form>

  <!---------------------------------------------------------------->
  <!------------------------ TABLE RESULT ------------------------>
  <!---------------------------------------------------------------->
  <div class="table-responsive-sm mt-2">
    <div id="tableBodyResult">
      <table class="table table-bordered table-sm" id="resultTable">
        <thead id="headerTable">
          <tr>
            <th class="th-sm accent-color primary-text-color">Date Time <i class="fa fa-clock-o"></i>
            </th>
            <th class="th-sm accent-color primary-text-color">Log In <i class="fa fa-pencil-square-o"></i>
            </th>
            <th class="th-sm accent-color primary-text-color">Log Out <i class="fa fa-pencil-square-o"></i>
            </th>
            <th class="th-sm accent-color primary-text-color">Employee Name <i class="fa fa-address-card"></i>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>2019-09-03</td>
            <td>7:58:00 AM</td>
            <td>6:04:20 PM</td>
            <td>Steven</td>
          </tr>
          <tr>
            <td>2019-09-04</td>
            <td>8:01:55 AM</td>
            <td>6:02:49 PM</td>
            <td>Steven</td>
          </tr>
          <tr>
            <td>2019-09-01</td>
            <td>7:31:55 AM</td>
            <td>6:03:49 PM</td>
            <td>Steven</td>
          </tr>
          <tr>
            <td>2019-09-03</td>
            <td>7:58:00 AM</td>
            <td>6:04:20 PM</td>
            <td>Steven</td>
          </tr>
          <tr>
            <td>2019-09-04</td>
            <td>8:01:55 AM</td>
            <td>6:02:49 PM</td>
            <td>Steven</td>
          </tr>
          <tr>
            <td>2019-09-01</td>
            <td>7:31:55 AM</td>
            <td>6:03:49 PM</td>
            <td>Steven</td>
          </tr>
          <tr>
            <td>2019-09-03</td>
            <td>7:58:00 AM</td>
            <td>6:04:20 PM</td>
            <td>Steven</td>
          </tr>
          <tr>
            <td>2019-09-04</td>
            <td>8:01:55 AM</td>
            <td>6:02:49 PM</td>
            <td>Steven</td>
          </tr>
          <tr>
            <td>2019-09-01</td>
            <td>7:31:55 AM</td>
            <td>6:03:49 PM</td>
            <td>Steven</td>
          </tr>
          <tr>
            <td>2019-09-03</td>
            <td>7:58:00 AM</td>
            <td>6:04:20 PM</td>
            <td>Steven</td>
          </tr>
          <tr>
            <td>2019-09-04</td>
            <td>8:01:55 AM</td>
            <td>6:02:49 PM</td>
            <td>Steven</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection