@extends('layouts.app')

@section('brandLink', route('home'))

@section('menuItems')
	<li class="{{ Ekko::isActiveRoute('add_word') }}"><a href="{{ route('add_word') }}">Add</a></li>
	<li class="{{ Ekko::isActiveRoute('random_word') }}"><a href="{{ route('random_word') }}">Quiz</a></li>
	<li class="{{ Ekko::isActiveURL('/logout') }}"><a href="/logout">Log Out</a></li>
@endsection