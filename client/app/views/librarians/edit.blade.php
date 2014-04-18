@extends('master')
@section('container')

  <h2>Konto</h2>

  {{ Form::model($user, array('class' => 'form-horizontal')) }}

    <div class="form-group">
      {{ Form::label('name', 'Navn', array('class' => 'col-sm-2 control-label')) }}
      <div class='col-sm-10'>
        {{ Form::text('name', null, array('class' => 'form-control')) }}
      </div>
    </div>

    <button type="submit" class="btn btn-primary">Lagre</button>

  {{ Form::close() }}

@stop
