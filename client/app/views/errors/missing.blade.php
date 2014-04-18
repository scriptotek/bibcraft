@extends('master')

@section('container')
	<h2>
		Uh oh
	</h2>
	<p>
		{{ isset($what) ? $what : 'Siden' }} finnes ikke.
	</p>
@stop
