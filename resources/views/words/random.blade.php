@extends('layouts.authenticated')

@section('title', 'Quiz')

@section('pageHeader', str_repeat('<i class="fa fa-question-circle-o"></i> ', 3))

@section('content')
	@include('words.partials.random')
@endsection

@push('scripts')
    <script type="text/template" id="pageHeaderTemplate">
	    @yield('pageHeader')
    </script>
    <script>
	    setFocusOnInput($("#answer"));

        $("#content").on("submit", "#answerForm", function(event) {
			event.preventDefault();
			$form = $(this);
			$.post($form.attr('action'), $form.serialize(), null, 'json')
			.done(function(data) {
				changePageHeader(data.correctAnswer);
				showResponse(data);
			});
		});

		$("#content").on("click", "#nextButton", function () {
			$.ajax({
				url: "{{ route('next_random_word') }}",
				dataType: "html",
				cache: false
			})
			.done(function(html) {
				showNextWord(html);
			});
		});
    </script>
@endpush