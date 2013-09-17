@extends('master')
@section('container')

<h2>Redigere bruker #{{$user->id}}</h2>

{{ Form::model($user, $formData) }}
	<div class="control-group">
		{{ Form::label('name', 'Navn') }}
	    <div class="controls">
	    	{{ Form::text('name') }}
	    </div>
	</div>

	<div class="control-group">
		{{ Form::label('phone', 'Mobil') }}
	    <div class="controls">
	    	{{ Form::text('phone') }}
	    </div>
	</div>

	<button type="submit" class="btn btn-primary">Lagre</button>

{{ Form::close() }}

@stop

@section('scripts')

<script type="text/javascript">

$(document).ready(function() {
	$('input[type="text"]').focus();
});

</script>

@stop