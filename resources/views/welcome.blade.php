@extends('layouts.guest')

@section('pageHeader', 'Home')

@section('content')
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1">
            <big>{{ trans('welcome.appShortDescription') }}</big>
            <p>{{ trans('welcome.appDescription') }}</p>
        </div>
    </div>
    <div class="row margin-top">
        <div class="col-xs-8 col-sm-offset-1 col-sm-7 col-md-offset-2 col-md-5">
            <img class="img-responsive" src="images/cow-tablet.png">
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2">
            <img class="img-responsive" src="images/bird_mobile.png">
        </div>
    </div>
@endsection
