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


@if ($e = $errors->all('<li>:message</li>'))
<div class="alert alert-error">
Kunne ikke lagre fordi:
<ul>
@foreach ($e as $msg)
  {{$msg}}
@endforeach
</ul>
</div>
@endif

    {{ Form::model($item, $formData) }}

      <h2>{{ $isNew ? 'Nytt objekt' : 'Rediger objekt' }}</h2>
      {{ Form::textWithLabelAndSearch('recordid', 'Objektid', 'Slå opp') }}
      {{ Form::textWithLabelAndSearch('isbn', 'ISBN', 'Slå opp') }}
      {{ Form::textWithLabel('title', 'Tittel') }}
      {{ Form::textWithLabel('subtitle', 'Undertittel') }}
      {{ Form::textWithLabel('authors', 'Forfatter(e) e.l.') }}
      {{ Form::textWithLabel('publisher', 'Forlag') }}
      {{ Form::textWithLabel('year', 'Utgivelsesår') }}
      {{ Form::textWithLabel('cover', 'Omslagsbilde-URL') }}
      {{ Form::textWithLabel('dewey', 'Dewey') }}
      {{ Form::textWithLabel('url', 'URL', 'URL til ressursen') }}
      {{ Form::textareaWithLabel('body', 'Beskrivelse') }}

      <a href="{{URL::action('ItemsController@getIndex')}}" class="btn">Avbryt</a>
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

  function sruTask(repo, query, cb) {
      $stat.append('<img src="{{ URL::to('/img/spinner2.gif') }}" title="' + repo + '" /> ');

      tasks += 1;
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
      tasks -= 1;
      $stat.find('img').first().remove();
      console.log(response.repo + ' lookup done, ' + tasks + ' tasks left');
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

      // Ask BIBSYS:

      sruTask('bibsys', query, function(response) {
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

  }

  function sruLookupsDone() {

      var authors = [],
          bibsysResults = results[0],
          url = 'http://ask.bibsys.no/ask/action/show?pid=' + bibsysResults.recordid + '&kid=biblio';

      $('#recordid').val(bibsysResults.recordid);
      $('#isbn').val(bibsysResults.isbn[0]);
      $('#title').val(bibsysResults.title.replace(/[\s]*:[\s]*$/, ''));
      $('#subtitle').val(bibsysResults.subtitle);
      $('#year').val(bibsysResults.year);
      for (var i=0; i < bibsysResults.authors.length; i++) {
          authors.push(firstnameFirst(bibsysResults.authors[i].name));
      }
      $('#authors').val(authors.join('; '));
      $('#publisher').val(bibsysResults.publisher);
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

      $('#control-recordid button').on('click', function(evt) {
          evt.preventDefault();
          sruLookups({ objektid: $('#recordid').val() }, $('#control-recordid button'));
      });

      $('#control-isbn button').on('click', function(evt) {
          evt.preventDefault();
          sruLookups({ isbn: $('#isbn').val() }, $('#control-isbn button'));
      });

      $('form').on('submit', function(e) {
          if ($("*:focus").is('#recordid')) {
              e.preventDefault();
              sruLookups({ objektid: $('#recordid').val() }, $('#control-recordid button'));
          } else if ($("*:focus").is('#isbn')) {
              e.preventDefault();
              sruLookups({ isbn: $('#isbn').val() }, $('#control-isbn button'));
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