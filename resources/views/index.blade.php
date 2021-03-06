@extends('layouts.app')

@section('content')

<div class="container mb-5">
  <!-- Error Message section -->
  <div class="container errorMsgs">
    <h5>Error List (Errors might be due to)</h5>
    <ul class="errorList">

    </ul>
  </div>
  <div class="lead text-white bg-info border rounded mb-2">
    <span class="ml-2">Note: Only your FIRST IN and LAST OUT are displayed on the results table</span>
    <br>
    <span class="ml-2">Note #2: Contact IT Support if you require access to "Branch" or other HRIS ID logs</span>
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
            @if (Auth::user()->staff_status)
              <option selected="" disabled="" value="">Choose Filter</option>
              <option value="1">HRIS ID</option>
              <option value="2">BRANCH</option>
              <option value="3">EMPLOYEE NAME</option>
            </select>
            <input type="text" class="form-control form-control-sm" id="filterInput" placeholder="" disabled>
            @else
              <option value="1" selected disabled>HRIS ID</option>
            </select>
            <input type="text" class="form-control form-control-sm" id="filterInput" placeholder="" disabled
              value="{{ Auth::user()->hrisid }}">
            @endif
          </div>
        </div>
        <!-- ANOTHER COLUMN -->
        <div class="col mb-2">
          <div class="d-sm-flex align-items-baseline">
            <span class="col-label mr-2">Branch:</span>
            <select class="form-control custom-select mr-sm-2 custom-select-sm" id="filterBranch">
              <option selected="" disabled="" value="">Choose Branch</option>
              @foreach ($branch as $item)
              {{ $item }}<option value={{ $item->bcode }}>{{ $item->bname }}</option>
              @endforeach
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
            <input type="text" class="form-control form-control-sm" id="date1" placeholder="1st Date" maxlength="10">
            <i class="fa fa-calendar mx-1"></i>
            <input type="text" class="form-control form-control-sm mr-sm-2" id="date2" placeholder="2nd Date"
              maxlength="10">
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
            <th class="th-sm accent-color primary-text-color">Date
            </th>
            <th class="th-sm accent-color primary-text-color">Log In
            </th>
            <th class="th-sm accent-color primary-text-color">Log Out
            </th>
            <th class="th-sm accent-color primary-text-color">Employee Name
            </th>
          </tr>
        </thead>
        <tbody>

        </tbody>
      </table>
    </div>
  </div>
</div>
<div id="cover-spin"></div>

@endsection

@section('extraJS')

<script type="text/javascript" src="{{ asset('js/jquery-ui.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/mainScripts/app.js') }}"></script>

@endsection