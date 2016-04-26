@extends('layouts.app')

@section('brandLink', route('welcome'))

@section('menuItems')
	<li class="{{ Ekko::isActiveRoute('welcome') }}"><a href="{{ route('welcome') }}">Home</a></li>
	<li class="{{ Ekko::isActiveURL('/login') }}"><a href="/login">Log In</a></li>
	<li class="{{ Ekko::isActiveURL('/register') }}"><a href="/register">Sign Up</a></li>
@endsection