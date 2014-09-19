@extends('master')
<?php

Form::macro('textWithLabelAndSearch', function($id, $label, $btn_label)
{
    $value = Form::getValueAttribute($id, null);
    return '
      <div class="form-group" id="control-' . $id . '">
        <label class="control-label col-sm-2" for="' . $id . '">' . $label . '</label>
        <div class="col-sm-6">
          <div class="input-group">
            <input class="form-control" type="text" id="' . $id . '" name="' . $id . '" value="' . $value . '" placeholder="' . $label . '">
            <span class="input-group-btn">
              <button type="button" class="btn btn-success" data-loading-text="Slår opp...">' . $btn_label . '</button>
            </span>
          </div>          
        </div>
      </div>';
});

Form::macro('textWithLabel', function($id, $label, $extras='')
{
    $value = Form::getValueAttribute($id, null);
    return '
      <div class="form-group">
        <label class="control-label col-sm-2" for="' . $id . '">' . $label . '</label>
        <div class="col-sm-6">
          <input class="form-control" type="text" id="' . $id . '" name="' . $id . '" value="' . $value . '" placeholder="' . $label . '">
        </div>
        <div class="col-sm-4">
          ' . $extras . '
        </div>
      </div>';
});

Form::macro('textareaWithLabel', function($id, $label, $extras='')
{
    $value = Form::getValueAttribute($id, null);
    return '
      <div class="control-group">
        <label class="control-label" for="' . $id . '">' . $label . '</label>
        <div class="controls">
          <textarea class="input-xlarge" rows="20" type="text" id="' . $id . '" name="' . $id . '">' . $value . '</textarea>' . $extras . '
        </div>
      </div>';
});

?>
@section('container')

    {{ Form::model($document, $formData) }}

      <h2>{{ $isNew ? 'Nytt dokument' : 'Rediger dokument' }}</h2>
      {{ Form::textWithLabel('bibsys_dokid', 'Dokid') }}
      {{ Form::textWithLabel('bibsys_knyttid', 'Knyttid') }}
      {{ Form::textWithLabel('bibsys_objektid', 'Objektid') }}
      {{ Form::textWithLabel('isbn', 'ISBN') }}
      {{ Form::textWithLabel('cover', 'Omslagsbilde-URL') }}
      {{ Form::textWithLabel('shelvinglocation', 'Samling') }}
      {{ Form::textWithLabel('callcode', 'Hylleplass') }}
      {{ Form::textWithLabel('title', 'Tittel') }}
      {{ Form::textWithLabel('authors', 'Forfatter(e) e.l.') }}
      {{ Form::textWithLabel('publisher', 'Forlag') }}
      {{ Form::textWithLabel('series', 'Serie') }}
      {{ Form::textWithLabel('volume', 'Bind') }}
      {{ Form::textWithLabel('year', 'Utgivelsesår') }}
      {{ Form::textWithLabel('dewey', 'Dewey') }}

      <div class="form-group">
        {{ Form::label('collections', 'Samling(er)', array('class' => 'control-label col-sm-2')) }}

        <div class="col-sm-6 control-group">

          {{ Form::select('collections[]', $collections,
            $collection ? array($collection) : (Session::has('_old_input') ? Session::has('_old_input.collections') : $document->collection_ids()),
            array(
              'multiple' => 'multiple',
              'style' => 'width: 100%;',
              'id' => 'collections'
          )) }}

        </div>
      </div>

      @if ($collection)
        <input type="hidden" name="collection" value="{{ $collection }}" />
      @endif

      <a href="{{URL::action('DocumentsController@getIndex')}}" class="btn">Avbryt</a>
      {{ Form::submit('Lagre', array('class' => 'btn btn-primary')) }}

    {{ Form::close() }}

@stop
@section('scripts')
<script type="text/javascript" src="/document-edit.js"></script>

@stop
