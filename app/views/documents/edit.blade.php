@extends('master')
<?php

Form::macro('textWithLabelAndSearch', function($id, $label, $btn_label)
{
    $value = Form::getValueAttribute($id, null);
    return '
      <div class="control-group" id="control-' . $id . '">
        <label class="control-label" for="' . $id . '">' . $label . '</label>
        <div class="controls">
          <div class="input-append">
            <input class="input-medium" type="text" id="' . $id . '" name="' . $id . '" value="' . $value . '" placeholder="' . $label . '">
            <button type="button" class="btn btn-success" data-loading-text="Slår opp...">' . $btn_label . '</button>
          </div>
            <span class="status"></span>
        </div>
      </div>';
});

Form::macro('textWithLabel', function($id, $label, $extras='')
{
    $value = Form::getValueAttribute($id, null);
    return '
      <div class="control-group">
        <label class="control-label" for="' . $id . '">' . $label . '</label>
        <div class="controls">
          <input class="input-xlarge" type="text" id="' . $id . '" name="' . $id . '" value="' . $value . '" placeholder="' . $label . '"><span style="padding-left: 10px;">' . $extras . '</span>
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
      {{ Form::textWithLabelAndSearch('bibsys_knyttid', 'Knyttid', 'Slå opp') }}
      {{ Form::textWithLabel('bibsys_dokid', 'Dokid') }}
      {{ Form::textWithLabel('bibsys_objektid', 'Objektid') }}
      {{ Form::textWithLabel('isbn', 'ISBN') }}
      {{ Form::textWithLabel('callcode', 'Hylleplass') }}
      {{ Form::textWithLabel('title', 'Tittel') }}
      {{ Form::textWithLabel('subtitle', 'Undertittel') }}
      {{ Form::textWithLabel('authors', 'Forfatter(e) e.l.') }}
      {{ Form::textWithLabel('publisher', 'Forlag') }}
      {{ Form::textWithLabel('series', 'Serie') }}
      {{ Form::textWithLabel('volume', 'Bind') }}
      {{ Form::textWithLabel('year', 'Utgivelsesår') }}
      {{ Form::textWithLabel('cover', 'Omslagsbilde-URL') }}
      {{ Form::textWithLabel('dewey', 'Dewey') }}

      <div class="control-group">
          {{ Form::label('collections', 'Samling(er)', array('class' => 'control-label')) }}

        <div class="controls">

          {{ Form::select('collections[]', $collections,
            $collection ? array($collection) : (Session::has('_old_input') ? Session::has('_old_input.collections') : $document->collection_ids()),
            array(
              'multiple' => 'multiple',
              'style' => 'width: 284px;',
              'id' => 'collections'
          )) }}

        </div>
      </div>

      @if ($collection)
        <input type="hidden" name="collection" value="$collection" />
      @endif

      <a href="{{URL::action('DocumentsController@getIndex')}}" class="btn">Avbryt</a>
      {{ Form::submit('Lagre', array('class' => 'btn btn-primary')) }}

    {{ Form::close() }}

@stop
@section('scripts')
<script type="text/javascript" src="/document-edit.js"></script>

@stop
