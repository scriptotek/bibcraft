(function() {

  var results = [],
    openLibraryResult = '',
    tasks = 0,
    $btn,
    $stat;

  function firstnameFirst(name) {
    var n = name.split(',', 2);
    return $.trim(n[1]) + ' ' + $.trim(n[0]);
  }

  function addTask(title) {
    console.log('Starting task: ' + title)
    $stat.append('<img src="/assets/spinner2.gif" title="' + title + '" /> ');
    tasks += 1;
  }

  function taskDone() {
    tasks -= 1;
    $stat.find('img').first().remove();
    console.log('Task done. ' + tasks + ' tasks left');
  }

  function openLibraryTask(isbn) {
    addTask('Open Library cover');
    var url = '//services2.biblionaut.net/open_library_cover.php';
    $.getJSON(url + '?isbn=' + isbn)
    .done(function(response) {
      if (response.url) {
        openLibraryResult = response.url;
        console.log('Open library returned: ' + response.url);
      }
      taskDone();
      checkFinish();
    })
    .error(function(e) {
      alert('Fikk ikke svar fra ' + url);
      tasks -= 1;
      checkFinish();
    });
  }

  function sruTask(repo, query, cb) {
    addTask(repo.toUpperCase() + ' catalogue');
    $.getJSON('//services.biblionaut.net/sru_iteminfo.php?repo=' + repo, query)
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
      console.log("---------------------------------------");
      console.log("COMPLETE!");
      $btn.button('reset');
      $stat.html('');
      console.log(results);
      sruLookupsDone();
    }
  }

  function sruLookups(query, $le_btn) {

    if ($('#title').val() != '') {
      if (!confirm('Dette vil overskrive eksisterende verdier. Er du sikker på at du vil fortsette?')) {
        return;
      }
    }

    $btn = $le_btn
    $btn.button('loading');
    $stat = $btn.parent().next('.status');
    $stat.html('');

    // TASK: ID lookup:

    addTask('IDs');
    $.getJSON('//services.biblionaut.net/getids.php?id=' + query.dokid)
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

      // TASK: Ask BIBSYS:

      sruTask('bibsys', {objektid: response.objektid}, function(response) {
        if (response.numberOfRecords == 0) {
          $('h2').after('<div class="alert alert-error"><a class="close" data-dismiss="alert" href="#">&times;</a> Fant ingen poster i ' + repo + '. </div>');
          return;
        }

        // Ask OpenLibrary
        if (response.isbn) {
          openLibraryTask(response.isbn[0]);

          // Ask LC:
          if (query.isbn === undefined) {
            query = { isbn: response.isbn[0] };
          }
          sruTask('loc', query, sruTaskDone);
        }

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

      // TASK: Holdings lookup:

      addTask('BIBSYS holdings');
      $.getJSON('//services.biblionaut.net/bibsys_holdings.php?id=' + query.dokid)
      .error(function() {
        alert("Fikk ikke svar fra " + repo + "...");
        taskDone();
      })
      .done(function(holdingsResponse) {
        taskDone();
        holdingsResponse.holdings.forEach(function(el) {
          console.log(el);
          if (el.id === response.dokid) {
            $('#callcode').val(el.shelvinglocation + ' ' + el.callcode);
          }
        });

      });
    });

  }

  function sruLookupsDone() {

      var authors = [],
          bibsysResults = results[0],
          url = 'http://ask.bibsys.no/ask/action/show?pid=' + bibsysResults.id + '&kid=biblio';

      $('#bibsys_objektid').val(bibsysResults.id);
      $('#isbn').val(bibsysResults.isbn ? bibsysResults.isbn[0] : '');
      $('#title').val(bibsysResults.title.replace(/[\s]*:[\s]*$/, ''));
      $('#subtitle').val(bibsysResults.subtitle);
      $('#year').val(bibsysResults.year);
      for (var i=0; i < bibsysResults.authors.length; i++) {
          authors.push(firstnameFirst(bibsysResults.authors[i].name));
      }
      $('#authors').val(authors.join('; '));
      $('#publisher').val(bibsysResults.publisher);
      $('#series').val(bibsysResults.series && bibsysResults.series.length > 0 ? bibsysResults.series[0].title : '');
      $('#volume').val(bibsysResults.volume);
      for (var i=0; i < bibsysResults.classifications.length; i++) {
          if (bibsysResults.classifications[i].system === 'dewey') {
              $('#dewey').val(bibsysResults.classifications[i].number);
          }
      }
      $('#url').val(url);

      for (var i = 0; i < results.length; i++) {
          if (results[i].cover_image && results[i].cover_image != '') {
              console.log('Fant bilde i bibsys');
              $('#cover').val(results[i].cover_image);
              updateCover();
          } else if (openLibraryResult != '') {
              console.log('Fant bilde i openlibrary');
              $('#cover').val(openLibraryResult);
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

})();