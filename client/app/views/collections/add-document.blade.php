@extends('master')
@section('container')

<h2><a href="{{ URL::action('CollectionsController@getShow', $collection->id) }}">
	{{ $collection->name }}
</a></h2>

<h3>Legg til dokument i samlingen</h3>

<form method="POST" action="{{ URL::action('CollectionsController@postAddDocument') }}">
	<label for="barcode">Strekkode</label>
	<input type="text" name="barcode" id="barcode" value="">
	<input type="hidden" name="collection" value="{{ $collection->id }}">

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