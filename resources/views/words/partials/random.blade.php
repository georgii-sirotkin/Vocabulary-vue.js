@if (is_null($word))
	@include('errors.partials.nowords')
@else
	@include('words.partials.imageAndDefinitions')

	<div class="space-for-stick-to-bottom">
	</div>

    <div class="stick-to-bottom button-panel">
	    <div class="row" id="formArea">
		    <div class="col-sm-offset-1 col-sm-10 col-md-9">
		        {!! Form::open(array('route' => 'check_answer', 'id' => 'answerForm')) !!}
			        <div class="input-group">
				        {!! Form::text('answer', null, ['class' => 'form-control', 'id' => 'answer']) !!}
				        <span class="input-group-btn">
					        <button type="submit" class="btn btn-primary" id="answerButton">Answer</button>
			            </span>
		            </div>
			    {!! Form::close() !!}
		    </div>
	    </div>
	    <div class="row" style="display: none" id="responseArea">
		    <div class="col-xs-8 col-sm-offset-1 col-sm-7">
		        <b id="responseMessage"></b>
	        </div>
	        <div class="col-xs-4 col-sm-3">
		        <button type="button" class="btn btn-default btn-block" id="nextButton">Next</button>
	        </div>
        </div>
    </div>
@endif