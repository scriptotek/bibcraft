@extends('master')
@section('container')

<h2>Ny samling</h2>

{{ Form::model($collection, $formData) }}

	{{ Form::label('name', 'Navn') }}
	{{ Form::text('name') }}

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