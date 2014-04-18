@extends('master')
@section('header')
  <h2>
    Dokumenter
  </h2>
  @if (isset($collection))
    <h3>
      {{ $collection->name }}
      ({{ count($documents) }} dokumenter)
    </h3>
  @endif
@stop
@section('container')

@if (Auth::check())
  <form role="form" class="hidden-print" style="margin-bottom: 10px;" method="post" action="{{ URL::action('DocumentsController@postStore') }}">

    <input type="hidden" name="collection" value="{{ $collection->id }}">

    <label for="barcode">Strekkode</label>
    <input type="text" name="barcode" id="barcode" value="">

    <button type="submit" class="btn btn-primary">
        <span class="glyphicon glyphicon-plus"></span>
        Legg til
    </button>
  </form>

@endif

  
  {{--
  <p style="float:right">
    Viser {{$from}}-{{$to}} av {{$total}}.
    Vis {{ Form::select('perPage', 
      array('5' => '5', '10' => '10', '25' => '25', '50' => '50', '100' => '100', '9999' => 'Alt'), 
      Input::get('itemsPerPage', Session::get('itemsPerPage', '10')), 
      array('id' => 'perPage', 'style' => 'width:60px;'))
    }}
    per side
  </p>
  --}}
<div id="books">
  <input type="text" class="search" placeholder="Søk">
  <ul class="list">

    @foreach ($documents as $obj)
    <li>

      <div class="cover">
        <img src="{{ $obj->cachedCover }}" alt="Cover">
      </div>

      <a href="http://ask.bibsys.no/ask/action/show?pid={{ $obj->bibsys_objektid }}&amp;kid=biblio" class="caption">
        {{ $obj->title }} {{ $obj->subtitle }}
      </a>

      <div>
        Av <em class="creator">{{ $obj->authors }}</em>
      </div>
      
      <div class="details">
        Utgitt: {{$obj->publisher }}, {{$obj->year }}<br> 
        Plass: <span class="location">
          {{ $obj->shelvinglocation }} {{ $obj->callcode }} (dokid {{ $obj->bibsys_dokid }})
        </span><br>
        
        <div class="hidden-print">
          {{-- Lagt til: {{ $obj->created_at }}<br>
          Samlinger:
          @if (count($obj->collections) == 0)
            <em>Ingen</em>
          @else
            @foreach ($obj->collections as $collection)
              <a href="{{URL::route('collectionDocuments', $collection->id)}}">{{$collection->name}}</a>
            @endforeach
          @endif --}}
          @if (count($obj->loans) > 0)
          <i class="icon-exclamation-sign"></i> UTLÅNT<br>
          @else
          <i class="icon-exclamation-sign"></i> LEDIG<br>
          @endif
        </div>

        <?php
        // If too many items are shown on the page, checking the imagesize for everyone would be sloooow
        /*if (intval(Input::get('itemsPerPage', Session::get('itemsPerPage', '10'))) < 50) {
          list($width, $height) = @getimagesize( public_path() . $obj->cachedCover );
          echo 'Omslagsbilde: ';
          if ($width) {
            echo $width . ' x ' . $height . ' px';
          } else {
            echo '<em>Mangler</em>';
          }
          echo '<br />';
        }*/
        ?>

        @if (Auth::check())
        <div class="hidden-print">
          <br>
          <a href="{{ URL::action('DocumentsController@getEdit', array(
            'id' => $obj->id, 
            'collection' => $collection->id,
            )) }}"><i class="icon-pencil"></i> rediger</a>
          <a href="{{ URL::action('CollectionsController@getRemoveDocument', array($collection->id, $obj->id)) }}"><i class="icon-trash"></i> fjern</a>
        </div>
        @endif
      </div>

      <div class="breaker"><!-- --></div>

    </li>
    @endforeach

  </ul>
  </div>
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

      $('#barcode').focus();

      var booksList = new List('books', {
        valueNames: [ 'caption', 'creator', 'location' ]
      });
      console.log(booksList);

      // $('#search').on('keypress', function(k) {
      //   console.log(booksList);
      //   var term = $('#search').val();
      //   booksList.search(term);
      // });

    });
  </script>
@endsection