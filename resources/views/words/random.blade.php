@extends('layouts.authenticated')

@section('title', 'Random')

@section('pageHeader')
	@for ($i = 0; $i < 3; $i ++)
		<i class="fa fa-question-circle-o"></i> 
	@endfor
@endsection

@section('content')
	@include('words.partials.random')
@endsection

@push('scripts')
    <script type="text/template" id="pageHeaderTemplate">
	    @yield('pageHeader')
    </script>
    <script>
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