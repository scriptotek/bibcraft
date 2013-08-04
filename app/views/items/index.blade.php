@extends('master')
@section('header')

    <a href="{{ URL::action('ItemsController@getCreate') }}" class="btn">
        <i class="icon-plus-sign"></i>
        Legg til
    </a>

@stop
@section('container')

<div style="overflow-y:auto; height:100%;">
  <ul>
@foreach ($items as $item)
    <li style="clear:both;">
      <img src="{{ URL::action('ItemsController@getCover', $item->id) }}" style="float:left; max-height: 120px; margin:3px 8px;">
      <a href="{{ URL::action('ItemsController@getShow', $item->id) }}">{{ $item->title }} {{ $item->subtitle }}</a><br />
      <a href="{{ URL::action('ItemsController@getShow', $item->id) }}">{{ $item->authors }} ({{$item->year }})</a><br />
      Lagt til: {{ $item->created_at }}<br />

      <a href="{{ URL::action('ItemsController@getPdf', $item->id) }}"><i class="icon-download"></i> pdf</a>
      <a href="{{ URL::action('ItemsController@getEdit', $item->id) }}"><i class="icon-pencil"></i> rediger</a>
      <a href="{{ URL::action('ItemsController@getDelete', $item->id) }}"><i class="icon-trash"></i> slett</a>
    </li>
@endforeach
  </ul>
</div>

@stop