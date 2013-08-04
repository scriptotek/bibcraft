@extends('master')
@section('header')
  <a href="{{ URL::action('ItemsController@getIndex') }}" class="btn"><i class="icon-arrow-left"></i> Lista</a>
  <a href="{{ URL::action('ItemsController@getPdf', $item->id) }}" class="btn"><i class="icon-download"></i> PDF</a>
  <a href="{{ URL::action('ItemsController@getEdit', $item->id) }}" class="btn"><i class="icon-pencil"></i> Rediger</a>
  <a href="{{ URL::action('ItemsController@getDelete', $item->id) }}" class="btn"><i class="icon-trash"></i> Slett</a>
  <a href="{{ $item->url }}" class="btn"><i class="icon-globe"></i> Følg URL</a>
@stop
@section('container')

  <div class="pagecontainer">
  
    <div style="color: #888888; font-size: 24px;">
      Utvalgt e-bok:
    </div>

    <div class="book-header">
      <h1>
        {{ $item->title }} 
      </h1>
      <h2>
        {{ $item->subtitle }} 
      </h2>

      <div style="color: black; font-size: 22px; line-height: 140%;">
        {{ $item->authors }}
        ({{ $item->year }})
      </div>
    </div>

    <table class="book-desc">
      <tr>

        <td class="col1">

          <div class="cover">
            <img src="{{ URL::action('ItemsController@getCover', $item->id) }}" />
          </div>

          <div class="qrcode">
            <img src="https://chart.googleapis.com/chart?chs=200x200&amp;cht=qr&amp;chl={{{ $itemUrl }}}&amp;choe=UTF-8" />
          </div>

        </td>
        <td class="col2">

            <div class="view">
              <?php
              $markdownParser = new MarkdownParser();
              echo $markdownParser->transformMarkdown($item->body);
              ?>
            </div>

            <textarea class="editor">{{{ $item->body }}}</textarea>

        </td>

      </tr>
    </table>

    <div class="book-footer">
      {{ $item->dewey }}
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
            url: "{{URL::action('ItemsController@putUpdate', $item->id)}}",
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