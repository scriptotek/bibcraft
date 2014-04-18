@extends('master')
@section('header')
    <h2>
      Utlån 
    </h2>
    <p>
        {{ count($loans) }} dokumenter er utlånt.
    </p>
@stop
@section('container')

  <form method="GET" action="{{ URL::action('LoansController@getSelect') }}">

    <div style="padding: 0 0 2em 0;">
      For de valgte dokumentene:
      <button type="submit" name="extend" class="btn btn-default">Forleng lånetid</button>
      <button type="submit" name="return" class="btn btn-default">Returnér</button>
      <button type="submit" name="remind" class="btn btn-default">Send påminnelse</button>
    </div>

    @foreach ($loans as $loan)

    <div class="row" style="margin-bottom:10px; padding: 0 0 10px 0; border-bottom: 1px solid #ccc;">

    	<div class="col-sm-1 checkboxColumn">
        <input type="checkbox" style="display: inline-block; height: 50px; vertical-align:middle;" name="loan_{{ $loan->id }}" class"hidden-print">

    		<div style="display:inline-block; vertical-align:middle; width:50px; height: 50px;">
          	<img src="{{ $loan->document->cachedCover }}" style="max-height: 50px; max-width: 50px; display: block; margin: auto;">
        	</div>
    	</div>

    	<div class="col-sm-6">
  		<strong>
  			{{ $loan->document->title }}
  			{{ $loan->document->subtitle ? ' : ' . $loan->document->subtitle : '' }}
    		</strong> 
    		({{$loan->document->bibsys_dokid}})<br>
    		{{$loan->document->callcode}}
  		<br>
    	</div>

    	<div class="col-sm-4">

  		  {{ $loan->user->name ?: '(mangler navn)' }}, {{ $loan->user->phone }}<br>

        @if ($loan->daysLeft() >= 0)
          <p class="bg-success">
            Forfaller om {{ $loan->daysLeft() }} dager
          </p>
        @else
          <p class="bg-danger">
            Forfalt for {{ - $loan->daysLeft() }} dager siden
          </p>
        @endif
        @if (is_null($loan->lastReminder() ))
          <p>
            Ingen påminnelser sendt
          </p>
        @else
          <p>
            Siste påminnelse sendt {{ $loan->lastReminder() == 0 ? 'i dag' : 'for ' . $loan->lastReminder() . ' dager siden' }}
          </p>
        @endif

    	</div>

    </div>

    @endforeach

  </form>

@stop

@section('scripts')
  <script type="text/javascript">
    $(document).ready(function() {

      $('.checkboxColumn').on('click', function(e) {
        if (!$(e.target).is(':checkbox')) {
          var cb = $(e.currentTarget).find(':checkbox');
          cb.prop('checked', !cb.prop('checked'));
        }
      });

    });
  </script>
@endsection
