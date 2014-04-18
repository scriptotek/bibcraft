@extends('master')

@section('container')

  <form method="POST" role="form" action="{{ URL::action('LibrariansController@postActivate') }}" class="panel panel-default form-horizontal">

    <div class="panel-heading">
      Aktiver konto
    </div>

    <div class="panel-body">

      <input type="hidden" name="activation_code" value="{{ $token }}">

      <div class="form-group">
        <label for="email" class="col-sm-2 control-label">Epost</label>
        <div class="col-sm-6">
          <p class="form-control-static">{{ $user->username }}</p>
        </div>
      </div>

      <div class="form-group">
        <label for="name" class="col-sm-2 control-label">Passord</label>
        <div class="col-sm-6">
          <input type="password" class="form-control" id="password" name="password">
        </div>
        <div class="col-sm-4">Minst 8 tegn. Ellers fritt frem</div>
      </div>

      <div class="form-group">
        <label for="name" class="col-sm-2 control-label">Gjenta passord</label>
        <div class="col-sm-6">
          <input type="password" class="form-control" id="password" name="password2">
        </div>
      </div>

    </div>

    <div class="panel-footer">
      <button type="submit" class="btn btn-success">Aktiver konto</button>
    </div>

  </form>

@stop

