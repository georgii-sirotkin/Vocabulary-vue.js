@extends('layouts.app')

@section('brandLink', route('home'))

@section('menuItems')
	<li class="{{ Ekko::isActiveRoute('words.create') }}"><a href="{{ route('words.create') }}">Add</a></li>
	<li class="{{ Ekko::isActiveRoute('random_word') }}"><a href="{{ route('random_word') }}">Quiz</a></li>
	<li><a href="/logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log Out</a></li>
	<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
		{{ csrf_field() }}
	</form>
@endsection