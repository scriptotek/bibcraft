@extends('master')
@section('container')

<h2>Slette samling?</h2>

{{ Form::model($collection, array(
            	'action' => array('CollectionsController@postDestroy', $collection->id),
                'method' => 'POST',
                'class' => 'form-horizontal')) }}

    <p>
    	Sikker på at du vil slette samlingen «{{$collection->name}}»? Hvis ikke; trykk på tilbakeknappen ;) Selv om du sletter en samling vil ikke selve dokumentene slettes.
	</p>
	<button type="submit" class="btn btn-danger">Ja, slett</button>

{{ Form::close() }}

@stop