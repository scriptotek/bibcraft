@extends('master')
@section('container')
    {{ Form::model($item, array(
        'action' => array('ItemsController@deleteDestroy', $item->id),
        'method' => 'DELETE',
        'class' => 'form-horizontal' 
    )) }}

      <h2>Slett objekt</h2>
      <p>
        Helt sikker?
      </p>

      {{ Form::submit('Jada', array('class' => 'btn btn-primary')) }}

    {{ Form::close() }}

@stop