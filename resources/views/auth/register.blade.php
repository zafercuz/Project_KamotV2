@extends('layouts.app')

@section('content')
<div class="container mb-5 pb-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="alert alert-info">
        <h5>Requirements</h5>
        <small class="font-weight-bold">
          <ul>
            <li>HRIS ID must be present at selected branch</li>
            <li>Email address should be the email you registered in HRIS</li>
            <li>Email address should match your inputted HRIS ID</li>
            <li>Password must at least be 8 characters length</li>
          </ul>
          Contact IT support if there is any problem
        </small>
      </div>
      <div class="card">
        <div class="card-header">{{ __('Register') }}</div>

        <div class="card-body">
          <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group row">
              <label for="branch" class="col-md-4 col-form-label text-md-right">{{ __('BRANCH') }}</label>

              <div class="col-md-6">
                <select class="form-control" name="branch" id="branch">
                  <option selected="" disabled="" value="">Choose Branch</option>
                  @foreach ($branch as $item)
                  <option value={{ $item->bcode }} {{ old('branch') == $item->bcode ? 'selected' : '' }}>
                    {{ $item->bname }}</option>
                  @endforeach
                </select>

                <span id="jsErrorMsg" class="text-danger font-weight-bold" style="font-size:14px;"></span>

              </div>
            </div>

            <div class="form-group row">
              <label for="hrisid" class="col-md-4 col-form-label text-md-right">{{ __('HRIS ID') }}</label>

              <div class="col-md-6">
                <input id="hrisid" type="text" class="form-control @error('hrisid') is-invalid @enderror" name="hrisid"
                  value="{{ old('hrisid') }}" autocomplete="hrisid" disabled>

                @error('hrisid')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>

            <div class="form-group row">
              <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

              <div class="col-md-6">
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                  value="{{ old('name') }}" autocomplete="name" disabled>

                @error('name')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>

            <div class="form-group row">
              <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

              <div class="col-md-6">
                <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email"
                  value="{{ old('email') }}" autocomplete="email" disabled>

                @error('email')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>

            <div class="form-group row">
              <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

              <div class="col-md-6">
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                  name="password" autocomplete="new-password" disabled>

                @error('password')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>

            <div class="form-group row">
              <label for="password-confirm"
                class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

              <div class="col-md-6">
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                  autocomplete="new-password" disabled>
              </div>
            </div>

            <div class="form-group row mb-0">
              <div class="col-md-6 offset-md-4">
                <button type="submit" class="btn btn-primary" id="registerBtn">
                  {{ __('Register') }}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="{{ asset('js/mainScripts/register.js') }}"></script>
@endsection