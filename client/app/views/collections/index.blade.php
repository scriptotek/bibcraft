@extends('master')
@section('header')
    <h2>Samlinger</h2>

    <a href="{{ URL::action('CollectionsController@getCreate') }}" class="btn">
        <i class="icon-plus-sign"></i>
        Ny samling
    </a>

@stop
@section('container')

  <ul>
@foreach ($collections as $collection)
    <li>
      <a href="{{URL::route('collectionDocuments', $collection->id)}}">
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