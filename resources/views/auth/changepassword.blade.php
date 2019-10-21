@extends('layouts.app')
@section('content')
<div class="container mb-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">Change password</div>

        <div class="card-body">
          @if (session('error'))
          <div class="alert alert-danger">
            {{ session('error') }}
          </div>
          @endif
          @if (session('success'))
          <div class="alert alert-success">
            {{ session('success') }}
          </div>
          @endif

          <form method="POST" action="{{ route('changePassword') }}">
            @csrf

            <div class="form-group row">
              <label for="current-password"
                class="col-md-4 col-form-label text-md-right">{{ __('Current Password') }}</label>

              <div class="col-md-6">
                <input id="current-password" type="password" class="form-control" name="current-password" required
                  autofocus>

                <span id="currentPassError" class="text-danger font-weight-bold" style="font-size:14px;"></span>
                @if ($errors->has('current-password'))
                <span class="help-block text-danger" style="font-size:14px;">
                  <strong>{{ $errors->first('current-password') }}</strong>
                </span>
                @endif
              </div>
            </div>

            <div class="form-group row">
              <label for="new-password" class="col-md-4 col-form-label text-md-right">{{ __('New Password') }}</label>

              <div class="col-md-6">
                <input id="new-password" type="password" class="form-control" name="new-password">

                <span id="newPassError" class="text-danger font-weight-bold" style="font-size:14px;"></span>
                @if ($errors->has('new-password'))
                <span class="help-block text-danger" style="font-size:14px;" id="backEndNewPassError">
                  <strong>{{ $errors->first('new-password') }}</strong>
                </span>
                @endif
              </div>
            </div>

            <div class="form-group row">
              <label for="new-password-confirm"
                class="col-md-4 col-form-label text-md-right">{{ __('Confirm New Password') }}</label>

              <div class="col-md-6">
                <input id="new-password-confirm" type="password" class="form-control" name="new-password_confirmation">

                <span id="newPassConfirmError" class="text-danger font-weight-bold" style="font-size:14px;"></span>
              </div>
            </div>

            <div class="form-group row mb-0">
              <div class="col-md-8 offset-md-4">
                <button type="submit" class="btn btn-primary" id="changePassBtn">
                  {{ __('Change Password') }}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="{{ asset('js/mainScripts/changePass.js') }}"></script>
@endsection