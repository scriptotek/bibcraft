@extends('master')
@section('header')  
  <h2>
    {{$collection->name}}
  </h2>
@stop
@section('container')


  <p style="float:right">
    Viser {{$from}}-{{$to}} av {{$total}}.
    Vis {{ Form::select('perPage', 
      array('5' => '5', '10' => '10', '25' => '25', '50' => '50', '100' => '100', '9999' => 'Alt'), 
      Input::get('itemsPerPage', Session::get('itemsPerPage', '10')), 
      array('id' => 'perPage', 'style' => 'width:60px;'))
    }}
    per side
  </p>

@if (Auth::check())
  <div class="hidden-print" style="margin-bottom: 10px;">
    <a href="{{ URL::action('DocumentsController@getCreate', array($collection->id)) }}" class="btn btn-default">
      <span class="glyphicon glyphicon-plus"></span>
      Legg til
    </a>
  </div>
<!--
  <div id="actionsForSelected" class="hidden-print" style="padding: 6px 6px; margin: 15px 0; border-radius:4px; margin-bottom: 10px; background: #ededed;">
  @if ($collection)
    Fjern merkede dokumenter fra samlingen? <button type="submit" class="btn">Fjern</button>
  @else
    Legg til merkede dokumenter i samling: {{ Form::select('collection', $collections, null, array('style' => 'width: 284px;' )) }}
    <button type="submit" class="btn">Legg til</button>
  @endif
  </div>-->
@endif


  @foreach ($documents as $obj)

  <div class="row" style="margin-bottom:10px; padding: 10px 0; background:#ececec; page-break-inside:avoid;">

    <!-- ************************************************
    We should make this into a template 

    <li style="margin:10px;padding: 10px; background:#ececec;">

  -->

    <div class="col-sm-2 hidden-print">

      <input type="checkbox" style="display: inline-block; height: 120px; vertical-align:middle;" name="check_{{ $obj->id }}">

      <div style="display:inline-block; vertical-align:middle; width:120px; height: 120px;">
        <img src="{{ $obj->cachedCover }}" style="max-height: 120px; max-width: 120px; display: block; margin: auto; box-shadow: 2px 2px 5px #888888;">
      </div>

    </div>

    <div class="col-sm-10">

      <strong><a href="http://ask.bibsys.no/ask/action/show?pid={{ $obj->bibsys_objektid }}&amp;kid=biblio">{{ $obj->title }} {{ $obj->subtitle }}</a></strong> ({{$obj->publisher }} {{$obj->year }})<br>
      Av: {{ $obj->authors }}<br>
      Dokid: {{ $obj->bibsys_dokid }}, samling: {{ $obj->shelvinglocation }}, plass: {{ $obj->callcode }}<br>
      <div class="hidden-print">
        Lagt til: {{ $obj->created_at }}<br>
        Samlinger:
        @if (count($obj->collections) == 0)
          <em>Ingen</em>
        @else
          @foreach ($obj->collections as $collection)
            <a href="{{URL::route('collectionDocuments', $collection->id)}}">{{$collection->name}}</a>
          @endforeach
        @endif
        @if (count($obj->loans) > 0)
        <i class="icon-exclamation-sign"></i> UTLÅNT<br>
        @endif
      </div>

      <?php
      // If too many items are shown on the page, checking the imagesize for everyone would be sloooow
      if (intval(Input::get('itemsPerPage', Session::get('itemsPerPage', '10'))) < 50) {
        list($width, $height) = @getimagesize( public_path() . $obj->cachedCover );
        echo 'Omslagsbilde: ';
        if ($width) {
          echo $width . ' x ' . $height . ' px';
        } else {
          echo '<em>Mangler</em>';
        }
        echo '<br />';
      }
      ?>

      @if (Auth::check())
      <div class="hidden-print">
        <br>
        <a href="{{ URL::action('DocumentsController@getEdit', $obj->id) }}"><i class="icon-pencil"></i> rediger</a>
        <a href="{{ URL::action('CollectionsController@getRemoveDocument', array($collection->id, $obj->id)) }}"><i class="icon-trash"></i> fjern</a>
      </div>
      @endif


    </div>

    <!-- ************************************************
    End of the template -->

  </div>

  @endforeach

@if (Auth::check())
</form>
@endif

  {{ $documents->links() }}


@stop

@section('scripts')
  <script type="text/javascript">
    $(document).ready(function() {

      $('#actionsForSelected').hide();
      $('input[type="checkbox"]').on('change', function() {
        if ($('input[type="checkbox"]:checked').length != 0) {
          $('#actionsForSelected').show();
        } else {
          $('#actionsForSelected').hide();
        }
      })

      $('#perPage').on('change', function() {
        var perPage = $('#perPage').val();
        window.location.href = '{{Request::url()}}?itemsPerPage=' + perPage;
      });

    });
  </script>
@endsection