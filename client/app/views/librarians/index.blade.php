@extends('master')
@section('header')
    <h2>
      Bibliotekarer
    </h2>
@stop
@section('container')


  @if (Auth::check())
    <div class="hidden-print" style="margin-bottom: 10px;">
      <a href="{{ URL::action('LibrariansController@getCreate') }}" class="btn btn-default">
        <span class="glyphicon glyphicon-plus"></span>
        Opprett ny bibliotekar
      </a>
    </div>
  @endif

  @foreach ($librarians as $obj)

  <div class="row" style="margin-bottom:10px; padding: 10px 0; background:#ececec; page-break-inside:avoid;">

    <!-- ************************************************
    We should make this into a template 

    <li style="margin:10px;padding: 10px; background:#ececec;">

  -->
    <div class="col-sm-12">
      {{ $obj->name }} / 
      {{ $obj->username }}

      @if (Auth::check())
      <div class="hidden-print">
        <br>
        <a href="{{ URL::action('LibrariansController@getEdit', $obj->id) }}"><i class="icon-pencil"></i> rediger</a>
        <a href="{{ URL::action('LibrariansController@getDelete', $obj->id) }}"><i class="icon-trash"></i> slett</a>
      </div>
      @endif


    </div>

    <!-- ************************************************
    End of the template -->

  </div>

  @endforeach

@stop

@section('scripts')
  <script type="text/javascript">
  </script>
@endsection