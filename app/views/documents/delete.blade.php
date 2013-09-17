@extends('master')
@section('container')
    {{ Form::model($document, array(
        'action' => array('DocumentsController@deleteDestroy', $document->id),
        'method' => 'DELETE',
        'class' => 'form-horizontal'
    )) }}

      <h2>Slett dokumentet {{$document->title}} {{$document->subtitle}} ? </h2>
      <p>
        Helt sikker?
      </p>

      {{ Form::submit('Jada, slett i vei', array('class' => 'btn btn-danger')) }}

    {{ Form::close() }}

@stop