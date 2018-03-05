@extends('layouts.authenticated')

@section('title', 'Quiz')

@section('pageHeader')
	<i class="fa fa-question-circle-o"></i>
	<i class="fa fa-question-circle-o"></i>
	<i class="fa fa-question-circle-o"></i>
@endsection

@section('content')
	@include('words.partials.random')
@endsection

@push('scripts')
    <script type="text/template" id="pageHeaderTemplate">
	    @yield('pageHeader')
    </script>
    <script>
	    setFocusOnInput($("#answer"));

	    var processingAnswer = false;
        $("#content").on("submit", "#answerForm", function(event) {
			event.preventDefault();
			if (processingAnswer) {
				return;
			}
			processingAnswer = true;
			processAnswer($(this));
		});

		var loadingNextWord = false;
		$("#content").on("click", "#nextButton", function () {
			if (loadingNextWord) {
				return;
			}
			loadingNextWord = true;
			loadNextWord("{{ route('next_random_word') }}");
		});
    </script>
@endpush