@extends('master')

@section('container')

  <form method="POST" action="{{ URL::action('LibrariansController@postStore') }}" class="panel panel-default">

    <div class="panel-heading">
      Ny bibliotekar      
    </div>

    <div class="panel-body">

      <div class="form-group">
        <label for="name" class="col-sm-2 control-label">Navn</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" id="name" name="name" placeholder="Navn">
        </div>
      </div>

      <div class="form-group">
        <label for="email" class="col-sm-2 control-label">Epost</label>
        <div class="col-sm-10">
          <input type="email" class="form-control" id="email" name="email" placeholder="Epost">
        </div>
      </div>

    </div>

    <div class="panel-footer">
      <a href="{{ URL::action('LibrariansController@getIndex') }}" class="btn">Avbryt</a>
      <button type="submit" class="btn btn-success">Lagre</button>
    </div>

  </form>

@stop

