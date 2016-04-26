@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/email') }}">
        {!! csrf_field() !!}

        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <label class="col-sm-4 control-label">E-Mail Address</label>

            <div class="col-sm-6">
                <input type="email" class="form-control" name="email" value="{{ old('email') }}">

                @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-3 col-sm-offset-4 col-md-2">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fa fa-btn fa-envelope"></i> Send
                </button>
            </div>
        </div>
    </form>
@endsection
