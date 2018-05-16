<div id="modal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title">{{ trans('modal.title') }}</h4>
			</div>
			<div class="modal-body">
		        <p>{{ trans('modal.greeting') }} {{ trans_choice('modal.numberOfWords', $numberOfWords, ['numberOfWords' => $numberOfWords]) }}</p>

		        @if ($numberOfWords < 4)
			        <p>{{ trans('modal.addWordInstruction') }}</p>
			        <p>{{ trans('modal.quizGuide') }}
		        @else
			        <p>{{ trans('modal.quizGuide') }}
			        <p>{{ trans('modal.addWordInstruction') }}</p>
		        @endif

		        <p>{{ trans('modal.haveFun') }} <i class="fa fa-smile-o" aria-hidden="true"></i></p>
			</div>
			<div class="modal-footer">
		        <button id="closeButton" type="button" class="btn btn-default" data-dismiss="modal">{{ trans('modal.close') }}</button>
			</div>
	    </div>
	</div>
</div>