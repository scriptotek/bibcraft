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
      {{ Form::textWithLabel('title', 'Tittel') }}
      {{ Form::textWithLabel('subtitle', 'Undertittel') }}
      {{ Form::textWithLabel('authors', 'Forfatter(e) e.l.') }}
      {{ Form::textWithLabel('publisher', 'Forlag') }}
      {{ Form::textWithLabel('serie', 'Serie') }}
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
<script type="text/javascript">

  function firstnameFirst(name) {
      var n = name.split(',', 2);
      return $.trim(n[1]) + ' ' + $.trim(n[0]);
  }

  var results = [],
    tasks = 0,
    $btn,
    $stat;

  function addTask(title) {
      $stat.append('<img src="{{ URL::to('/img/spinner2.gif') }}" title="' + title + '" /> ');
      tasks += 1;
  }

  function taskDone() {
      tasks -= 1;
      $stat.find('img').first().remove();
  }

  function sruTask(repo, query, cb) {
      addTask(repo);
      $.getJSON('http://labs.biblionaut.net/services/sru_iteminfo.php?repo=' + repo + '&callback=?', query)
      .done(function(response) {
          response.repo = repo;
          results.push(response);
          cb(response);
      })
      .error(function() {
          alert("Fikk ikke svar fra " + repo + "...");
          tasks -= 1;
          checkFinish();
      });
  }

  function sruTaskDone(response) {
      taskDone();
      checkFinish();
  }

  function checkFinish() {
      if (tasks === 0) {
          console.log("COMPLETE!");
          $btn.button('reset');
          $stat.html('');
          console.log(results);
          sruLookupsDone();
      }
  }

  function sruLookups(query, $le_btn) {

      if ($('#title').val() != "") {
          if (!confirm("Dette vil overskrive eksisterende verdier. Er du sikker på at du vil fortsette?")) {
              return;
          }
      };

      $btn = $le_btn
      $btn.button('loading');
      $stat = $btn.parent().next('.status');
      $stat.html('');

      // ID lookup:
      addTask('ID lookup');
      $.getJSON('http://labs.biblionaut.net/services/getids.php?id=' + query.dokid + '&callback=?')
      .error(function() {
          alert("Fikk ikke svar fra " + repo + "...");
          taskDone();
      })
      .done(function(response) {
        taskDone();
        if (query.dokid != response.dokid) {
          $('#bibsys_knyttid').val(query.dokid);
        } else {
          $('#bibsys_knyttid').val('');
        }
        $('#bibsys_dokid').val(response.dokid);
        $('#bibsys_objektid').val(response.objektid);

        // Ask BIBSYS:

        sruTask('bibsys', {objektid: response.objektid}, function(response) {
            if (response.numberOfRecords == 0) {
                $('h2').after('<div class="alert alert-error"><a class="close" data-dismiss="alert" href="#">&times;</a> Fant ingen poster i ' + repo + '. </div>');
                return;
            }

            // Ask LC:
            if (query.isbn === undefined) {
                query = { isbn: response.isbn[0] };
            }
            sruTask('loc', query, sruTaskDone);


            // Ask LC and BIBSYS about other form:
            if (response.other_form !== undefined) {
                var ofo = response.other_form;
                console.log('Other form found:');
                console.log(ofo);
                sruTask('bibsys', { isbn: ofo.isbn }, sruTaskDone);
                sruTask('loc', { isbn: ofo.isbn }, sruTaskDone);
            }

            sruTaskDone(response);

        });

      });

  }

  function sruLookupsDone() {

      var authors = [],
          bibsysResults = results[0],
          url = 'http://ask.bibsys.no/ask/action/show?pid=' + bibsysResults.recordid + '&kid=biblio';

      $('#bibsys_objektid').val(bibsysResults.recordid);
      $('#isbn').val(bibsysResults.isbn[0]);
      $('#title').val(bibsysResults.title.replace(/[\s]*:[\s]*$/, ''));
      $('#subtitle').val(bibsysResults.subtitle);
      $('#year').val(bibsysResults.year);
      for (var i=0; i < bibsysResults.authors.length; i++) {
          authors.push(firstnameFirst(bibsysResults.authors[i].name));
      }
      $('#authors').val(authors.join('; '));
      $('#publisher').val(bibsysResults.publisher);
      $('#series').val(bibsysResults.series);
      $('#volume').val(bibsysResults.volume);
      for (var i=0; i < bibsysResults.klass.length; i++) {
          if (bibsysResults.klass[i].system === 'dewey') {
              $('#dewey').val(bibsysResults.klass[i].kode);
          }
      }
      $('#url').val(url);

      for (var i = 0; i < results.length; i++) {
          if (results[i].cover_image && results[i].cover_image != '') {
              $('#cover').val(results[i].cover_image);
              updateCover();
          }
          if (results[i].summary !== undefined) {
              $('#body').val(results[i].summary.text);
          }
      }

  }

  function updateCover() {
      console.log('updating cover');

      $('#cover').data('content', '<img alt="Bildet ble ikke funnet" src="' + $('#cover').val() + '" style="max-width:150px;" />')
                 .popover('show');

  }

  $(document).ready(function() {

      $('#collections').select2();

      $('#bibsys_knyttid').focus();

      $('#control-bibsys_knyttid button').on('click', function(evt) {
          evt.preventDefault();
          sruLookups({ dokid: $('#bibsys_knyttid').val() }, $('#control-bibsys_knyttid button'));
      });

      $('form').on('submit', function(e) {
          if ($("*:focus").is('#bibsys_knyttid')) {
              e.preventDefault();
              if ($('#bibsys_knyttid').val() !== '') {
                  $('#bibsys_dokid').focus();
                  sruLookups({ dokid: $('#bibsys_knyttid').val() }, $('#control-bibsys_knyttid button'));
              }
          }

      });

      var coverHtml = $('#cover').val() ? '<img alt="Bildet ble ikke funnet" src="' + $('#cover').val() + '" style="max-width:150px;" />' : 'n/a';
      $('#cover').data('title', 'Forhåndsvis omslagsbilde')
                 .data('content', coverHtml)
                 .popover({
                            html: true,
                            content: function (){
                              return $(this).data('content')
                            },
                            trigger: 'manual'
                         })
                 .popover('show');

      $('#cover').on('change', updateCover);

      $(window).resize(function() {
          $('#cover').popover('show');
      });

  });

</script>

@stop