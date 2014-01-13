@extends('master')
@section('header')
    <h2>
      Dokumenter
      @if ($collection)
        i samlingen «{{$collection->name}}»
      @endif
    </h2>
@stop
@section('container')

@if ($collection)

<form class="form-inline" method="post" action="{{ URL::action('CollectionsController@postRemoveFromCollection', $collection->id) }}">
@else
<form class="form-inline" method="post" action="{{ URL::action('CollectionsController@postAddToCollection') }}">
@endif

  <p style="float:right">
    Viser {{$from}}-{{$to}} av {{$total}}.
    Vis {{ Form::select('perPage', array('5' => '5', '10' => '10', '25' => '25', '50' => '50', '100' => '100'), Input::get('itemsPerPage', Session::get('itemsPerPage', '10')), array('id' => 'perPage', 'style' => 'width:60px;')) }}
    per side
  </p>

  <div class="hidden-print">
    @if ($collection)
    <a href="{{ URL::action('DocumentsController@getCreate') }}?collection={{$collection->id}}" class="btn">
      <i class="icon-plus-sign"></i>
      Nytt dokument
    </a>
    @else
    <a href="{{ URL::action('DocumentsController@getCreate') }}" class="btn">
      <i class="icon-plus-sign"></i>
      Nytt dokument
    </a>
    @endif
  </div>

  <div id="actionsForSelected" class="hidden-print" style="padding: 6px 6px; margin: 15px 0; border-radius:4px; background: #ededed;">
  @if ($collection)
    Fjern merkede dokumenter fra samlingen? <button type="submit" class="btn">Fjern</button>
  @else
    Legg til merkede dokumenter i samling: {{ Form::select('collection', $collections, null, array('style' => 'width: 284px;' )) }}
    <button type="submit" class="btn">Legg til</button>
  @endif
  </div>



  <ul style="list-style-type:none; clear:both;margin-top:20px;">
  @foreach ($documents as $obj)

    <!-- ************************************************
    We should make this into a template -->
    <li style="margin:10px;padding: 10px; background:#ececec;">
      <input type="checkbox" style="float:left; display: block; height: 120px; width:30px;" name="check_{{ $obj->id }}">
      <div style="float:left; width:120px; height: 120px; margin:3px 8px;">
        <img src="{{ $obj->cachedCover }}" style="max-height: 120px; max-width: 120px; display: block; margin: auto; box-shadow: 2px 2px 5px #888888;">
      </div>
      <strong>{{ $obj->title }} {{ $obj->subtitle }}</strong> ({{$obj->publisher }} {{$obj->year }})<br>
      Av: {{ $obj->authors }}<br>
      Dokid: {{ $obj->bibsys_dokid }}, plass: {{ $obj->callcode }}<br>
      Lagt til: {{ $obj->created_at }}<br>
      Samlinger:
      @if (count($obj->collections) == 0)
        <em>Ingen</em>
      @else
        @foreach ($obj->collections as $collection)
          <a href="{{URL::route('collectionDocuments', $collection->id)}}">{{$collection->name}}</a>
        @endforeach
      @endif
      <br>

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
      <br />


      <div class="hidden-print">
        <a href="{{ URL::action('DocumentsController@getEdit', $obj->id) }}"><i class="icon-pencil"></i> rediger</a>
        <a href="{{ URL::action('DocumentsController@getDelete', $obj->id) }}"><i class="icon-trash"></i> slett</a>
      </div>
      <div style="clear:both;"></div>
    </li>
    <!-- ************************************************
    End of the template -->

  @endforeach
  </ul>

</form>

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