(function() {

  var results = [],
    openLibraryResult = '';

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

  function updateCover() {
      console.log('updating cover');

      $('#cover').data('content', '<img alt="Bildet ble ikke funnet" src="' + $('#cover').val() + '" style="max-width:150px;" />')
                 .popover('show');

  }

  $(document).ready(function() {

      $('#collections').select2();

      $('#cover').focus();

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