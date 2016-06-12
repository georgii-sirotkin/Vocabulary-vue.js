@extends('layouts.guest')

@section('content')
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-center hidden-xs hidden-sm">
                        <b>Log in with social media</b>
                    </p>
                    <p>
                        <a href="{{ route('third_party_login', ['provider' => 'facebook']) }}" class="btn btn-block btn-social btn-facebook">
                          <span class="fa fa-facebook"></span>
                          Log in with Facebook
                        </a>
                    </p>
                    <p>
                        <a href="{{ route('third_party_login', ['provider' => 'google']) }}" class="btn btn-block btn-social btn-google">
                          <span class="fa fa-google"></span>
                          Log in with Google
                        </a>
                    </p>
                </div>
                <hr class="hidden-md hidden-lg">
                <div class="col-md-6 authentication-form">
		            @yield('authForm')
                </div>
            </div>
        </div>
    </div>
@endsection