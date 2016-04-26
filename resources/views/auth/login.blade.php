@extends('layouts.auth')

@section('title', 'Log In')

@section('authForm')
    <form role="form" method="POST" action="{{ url('/login') }}">
        {!! csrf_field() !!}

        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <label>E-Mail Address</label>
            <input type="email" class="form-control" name="email" value="{{ old('email') }}">
            @if ($errors->has('email'))
                <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
            <label>Password</label>
            <input type="password" class="form-control" name="password">
            @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        </div>

        <div class="checkbox">
            <label>
                <input type="checkbox" name="remember"> Remember Me
            </label>
        </div>
        <div>
            <div class="col-sm-5 padding-0">
                <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-btn fa-sign-in"></i> Log In</button>
            </div>
            <div class="col-sm-6 col-sm-offset-1 padding-0">
                <a class="btn btn-link padding-0" href="{{ url('/password/reset') }}">Forgot Password?</a>
            </div>
        </div>

    </form>
@endsection
