@extends('master')

@section('container')

  <h2>Forleng disse med 4 uker?</h2>

  <form method="POST" action="{{ URL::action('LoansController@postExtend') }}">

    <input type="hidden" name="loans" value="{{ implode(',', $loan_ids) }}">

    <ul class="list-group">
    @foreach ($loans as $loan)
      <li class="list-group-item">
        <h4 class="list-group-item-heading">          
          {{ $loan->document->title }}
          {{ $loan->document->subtitle ? ' : ' . $loan->document->subtitle : '' }}
        </h4> 
        ({{$loan->document->bibsys_dokid}})<br>
        {{$loan->document->callcode}}
      </li>
    @endforeach 
    </ul>

    <div class="panel-footer">
      <a href="{{ URL::action('LoansController@getIndex') }}" class="btn">Avbryt</a>
      <button type="submit" class="btn btn-success">Forleng</button>
    </div>

  </form>

@stop

