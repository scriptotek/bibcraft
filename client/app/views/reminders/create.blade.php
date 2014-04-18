@extends('master')

@section('container')

  <form method="POST" action="{{ URL::action('RemindersController@postStore') }}">

    <ul class="list-group">
    @foreach ($reminders as $reminder)
      <li class="list-group-item">
        <h4 class="list-group-item-heading">Til: {{$reminder['user']->phone}} {{$reminder['user']->name ? '(' . $reminder['user']->name . ')' : '' }} </h4>
        <ul>
        @foreach ($reminder['loans'] as $loan)
          <li>
          {{$loan->document->bibsys_dokid}}:  {{$loan->document->title}} (utlånt {{$loan->created_at}})
          </li>
        @endforeach
        </ul>

        <input type="hidden" name="loans_{{$reminder['idx']}}" value="{{$reminder['loan_ids']}}">
        <textarea style="width: 600px; height: 60px;" name="msg_{{$reminder['idx']}}">{{$reminder['msg']}}</textarea>

      </li>
    @endforeach 
    </ul>

    <div class="panel-footer">
      <a href="{{ URL::action('LoansController@getIndex') }}" class="btn">Avbryt</a>
      <button type="submit" class="btn btn-success">Send</button>
    </div>

  </form>

@stop

