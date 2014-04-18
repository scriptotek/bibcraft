@extends('master')
@section('container')

<h2>
	Registrer dokument
</h2>

<form method="POST" action="{{ URL::action('DocumentsController@postStore') }}">

@if ($collection)
	<h3>
		<a href="{{ URL::action('DocumentsController@getIndex', array('collection' => $collection->id)) }}">
			{{ $collection->name }}
		</a>		
	</h3>
	<input type="hidden" name="collection" value="{{ $collection->id }}">
@endif

	<label for="barcode">Strekkode</label>
	<input type="text" name="barcode" id="barcode" value="">

	<button type="submit" class="btn btn-primary">Legg til</button>

</form>

@stop

@section('scripts')

<script type="text/javascript">

$(document).ready(function() {
	$('input[type="text"]').focus();
});

</script>

@stop