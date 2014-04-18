@extends('master')
@section('header')
    <h2>Samlinger</h2>

    <a href="{{ URL::action('CollectionsController@getCreate') }}" class="btn btn-default">
      <span class="glyphicon glyphicon-plus"></span>
      Ny samling
    </a>

@stop
@section('container')


<ul class="list-group">
@foreach ($collections as $collection)
  <li class="list-group-item">
    <a href="{{ URL::action('DocumentsController@getIndex', array('collection' => $collection->id )) }}">
    	{{ $collection->name }}
    </a><br />

    Opprettet: {{ $collection->created_at }}<br />

    Antall dokumenter: {{ count($collection->documents) }}<br />

    <a href="{{ URL::action('CollectionsController@getEdit', $collection->id) }}"><i class="icon-pencil"></i> rediger</a>
    <a href="{{ URL::action('CollectionsController@getDelete', $collection->id) }}"><i class="icon-trash"></i> slett</a>
  </li>
@endforeach
</ul>

@stop