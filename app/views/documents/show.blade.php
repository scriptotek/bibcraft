@extends('master')
@section('header')
  <a href="{{ URL::action('DocumentsController@getIndex') }}" class="btn"><i class="icon-arrow-left"></i> Lista</a>
  <a href="{{ URL::action('DocumentsController@getEdit', $document->id) }}" class="btn"><i class="icon-pencil"></i> Rediger</a>
  <a href="{{ URL::action('DocumentsController@getDelete', $document->id) }}" class="btn"><i class="icon-trash"></i> Slett</a>
  <a href="{{ $document->url }}" class="btn"><i class="icon-globe"></i> Følg URL</a>
@stop
@section('container')

  <div class="pagecontainer">

    <div class="book-header">
      <h1>
        {{ $document->title }}
      </h1>
      <h2>
        {{ $document->subtitle }}
      </h2>

      <div style="color: black; font-size: 22px; line-height: 140%;">
        {{ $document->authors }}
        ({{ $document->year }})
      </div>
    </div>

    <table class="book-desc">
      <tr>

        <td class="col1">

          <div class="cover">
            <img src="{{ $document->cachedCover }}" />
          </div>

        </td>
        <td class="col2">

            <div class="view">
              {{ $document->body }}
            </div>

            <textarea class="editor">{{{ $document->body }}}</textarea>

        </td>

      </tr>
    </table>

    <div class="book-footer">
      {{ $document->dewey }}
    </div>

  </div>

@stop
@section('scripts')

<script type="text/javascript">

  $(function(){

    var editing = false
      , saving = false;

    $('.book-desc .view').on('click', function(e) {
      console.info('Viewing → Editing');
      editing = true;
      var h = $('.book-desc .view').height();

      $('.book-desc .view').hide();

      $('.book-desc .editor')
        .height(h + 50)
        .show()
        .focus();
    });

    $('body').on('click', function(e) {
      console.log(e.target);

      if ( !$(e.target).is('.book-desc .view, .book-desc .editor') && $(e.target).parents('.book-desc .view, .book-desc .editor').length == 0 ) {

        if (editing && !saving) {
          console.info('Editing → Saving');
          editing = false;
          saving = true;
          $.ajax({
            url: "{{URL::action('DocumentsController@putUpdate', $document->id)}}",
            dataType: 'json',
            method: 'PUT',
            data: { body: $('.book-desc textarea').val() }
          }).done(function(data) {
          console.info('Saving → Viewing');
            $('.book-desc .view')
              .html(data.html)
              .show();
            $('.book-desc .editor')
              .val(data.item.body)
              .hide();
            saving = false;
          });

        }

      };
    });

    var $quote = $("h1");

    // var $numWords = $quote.text().split(" ").length;
    // if (($numWords >= 1) && ($numWords < 10)) {
    //     $quote.css("font-size", "36px");
    // }
    // else if (($numWords >= 10) && ($numWords < 20)) {
    //     $quote.css("font-size", "32px");
    // }
    // else if (($numWords >= 20) && ($numWords < 30)) {
    //     $quote.css("font-size", "28px");
    // }
    // else if (($numWords >= 30) && ($numWords < 40)) {
    //     $quote.css("font-size", "24px");
    // }
    // else {
    //     $quote.css("font-size", "20px");
    // }

  });

</script>

@stop