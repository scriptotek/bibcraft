
//(function ($) {

    "use strict";
    var connection,
        person_data,
        my_items = [];


    function log(msg, type) {
        var s = '<br />';
        if (type == 'error') {
            s += '<span style="color: red;">FEIL:</span> ';
        } else if (type == 'warn') {
            s += '<span style="color: orange;">MERK:</span> ';
        } else if (type == 'info') {
            s += '<span style="color: green;">INFO:</span> ';
        }
        $('.credit').append(s + msg);
        $('#footer .container').stop().animate({ scrollTop: $("#footer .container")[0].scrollHeight }, 800);
    }

    function get_dokid(book_id, callback) {
        // Get object id from knyttid
        var url = '//labs.biblionaut.net/services/getids.php?id=' + book_id;
        $.get(url, function(response) {
            console.log(response);
            var dokid = response.dokid.trim();
            if (dokid.length > 6) {
                callback(dokid);
            }
        });
    }

    function found_tag(tag) {

        //log('Found tag');
        //console.log(tag);

        if (tag.is_blank) {
            log('Found blank tag');
            found_blank_tag(tag.uid);
            return;
        } else {
            log('Found ' + tag.usage_type + ', id: ' + tag.id);
            if (tag.usage_type == 'for-circulation') {
                $('audio')[0].play();
            } else if (tag.usage_type == 'patron-card') {
                $('audio')[1].play();
            }
        }

        if ($('#slide1').is(':visible') || $('#slide2').is(':visible')) {
            if (tag.usage_type == 'for-circulation') {
                if ($('#slide1').is(':visible')) {
                    $('#slide1 p').fadeOut();
                    $('#slide1 h1').html(tag.id);
                }
                get_dokid(tag.id, function(dokid) {
                    if ($('#slide1').is(':visible')) {
                        $('#slide1 h1').html(dokid);
                    }
                    lookup_item_info(dokid);
                });
            }
        }

        if ($('#slide1').is(':visible')) {
            if (tag.usage_type == 'patron-card') {
                minelan(tag.id);
            }
        }

        if ($('#slide3').is(':visible')) {
            if (tag.usage_type == 'patron-card') {
                var patron = tag.id;
                log('FOUND PATRON CARD', patron);
                lookup_patron_info(patron);
            }
        }

        if ($('#slide5').is(':visible')) {
            $('#slide5 p:last').append('<br />Fant et ikke-blankt kort. Prøv igjen med et annet kort.');
        }

    }

    function showLoan(loan) {
        var obj = loan.document.object;
        $('#myloans').append('<li class="loan"><a href="">' + obj.title + '</a></li>');
    }

    function minelan(patron_id) {
        $('#myloans').html('<li>Sjekker<li>');
        $('.slide').hide();
        $('#slide9').fadeIn();

        $.get('/users/loans/' + patron_id, function(loans) {
            $('#myloans').html('');
            for (var i = loans.length - 1; i >= 0; i--) {
                showLoan(loans[i]);
            };
        });
    }

    function found_blank_tag(uid) {
        if ($('#slide5').is(':visible')) {
            connection.send(JSON.stringify({
                rcpt: 'backend',
                msg: 'write-patron-card',
                uid: uid,
                data: person_data
            }));
        }
    }

    function lookup_patron_info(patron_id) {
        log('Looking up patron info for ' + patron_id, 'info');
        $.get('/users/show?patron_id=' + patron_id, function(patron) {
            console.log(patron);

            // decode html entities in a funny way:
            var pname = $('<div />').html(patron.name).text();

            $('#slide3').hide();
            $('#slide5').hide();
            $('#slide6').fadeIn();
            $('#patron-name').html(pname);
            log('Registrerer lån...', 'info');
            $.post('/users/add-loans', {
                patron_id: patron_id,
                items: JSON.stringify(my_items)
            }, function(res2) {
                log('Lån registrert', 'info');
                console.log(res2);
                $('#newloans').append('<br />Hurra, ' + res2.loans.length + ' lån registrert!<br /><br /><a href="/bibcraft/minelan?userid=' + patron_id + '" class="btn bnt-large">Mine lån</a>');
            });
        });
    }

    function lookup_item_info(dokid) {
        log('Looking up item info for ' + dokid);
        $.get('/documents/show/' + dokid, function(itm) {
            $('#slide1').hide();
            $('#slide2').fadeIn();
            if (itm.length == 0) {
                log('Dokumentet ' + dokid + ' ble ikke funnet i basen.', 'warn');

                $('#borrowing-cart').append('<div class="alert"> \
                    <button type="button" class="close" data-dismiss="alert">&times;</button> \
                    <strong>Åh nei!</strong> Boka finnes ikke i basen!\
                    </div>');
                $('#borrowing-cart .item:last').slideDown('fast', function() {
                    var $t = $('#borrowing-cart');
                        $t.animate({"scrollTop": $('#borrowing-cart')[0].scrollHeight}, "slow");
                });
                $('#sfx-error')[0].play();
            } else {
                var obj = itm.object;
                console.log(itm);
                my_items.push(itm.id);
                var code = ' \
                    <div class="item" style="display: none;" > \
                      <table> \
                        <tr> \
                          <td> \
                            <img class="cover" src="/objects/cover/' + obj.id + '" alt="Cover image" /> \
                          </td> \
                          <td> \
                            <span class="field title">' + obj.title + '</span> \
                            <em>by</em> <span class="field author">' + obj.authors + '</span> \
                            <span class="field pubyear">' + obj.year + '</span> \
                          </td> \
                        </tr> \
                      </table> \
                    </div>';
                $('#borrowing-cart').append(code);
                $('#borrowing-cart .item:last').slideDown('fast', function() {
                    var $t = $('#borrowing-cart');
                        $t.animate({"scrollTop": $('#borrowing-cart')[0].scrollHeight}, "slow");
                });
            }
            var nbooks = $('#borrowing-cart .item').length;
            if (nbooks == 1) {
                $('#loan-btn').html('Lån boka');
            } else {
                $('#loan-btn').html('Lån de ' + nbooks + ' bøkene');
            }
        });
    }

    function lookup_person(tlfnr) {
        $('#slide3').hide();
        $('#slide7').fadeIn();

        log('Checking if ' + tlfnr + ' is already registered');
        $.get('/users/show', { phone: tlfnr }, function(res0) {
            if (res0.length > 0) {
                log('Det finnes allerede en registrert bruker med dette telefonnummeret!', 'warn');
                $.get('/users/newActivationCode', { user_id: res0[0].user_id }, function(res1) {
                    $('#slide7').hide();
                    $('#slide8').fadeIn();
                    $('#bekreftelse2').val();
                });
            } else {

                log('Forsøker å finne navn knyttet til ' + tlfnr, 'info');
                $.getJSON('//labs.biblionaut.net/services/getpersonname.php?number=' + tlfnr, function(res) {

                    // decode html entities in a funny way:
                    var pname = $('<div />').html(res.personname).text();

                    if (pname == 'uunknown') {
                        log('Fant ikke personen', 'error');
                    } else {
                        $('#slide7').hide();
                        $('#slide4').fadeIn();
                        $('#bekreftelse').closest('.control-group').removeClass('error');


                        log('Legger til ny bruker: ' + pname + ' (' + res.number + ')', 'info');
                        $.post('/users/store', { name: pname, number: res.number }, function(res2) {
                            if (res2.error === '') {
                                $('#slide3').hide();
                                $('#slide4').show();
                                $('#nummer').val(res2.phone);
                                $('#bekreftelse').val('');
                                $('#personnavn').val(pname);
                            } else {
                                if (res2.error == 'eksisterer allerede') {
                                    alert('Brukeren eksisterer allerede (men er kanskje ikke aktivert?)');
                                } else {
                                    alert('Ble ikke lagret: ' + res2.error);
                                }
                            }
                        });
                    }

                });
            }
        });
    }

    function confirm_person() {
        person_data = {
            number: $('#nummer').val(),
            confirmation: $('#bekreftelse').val(),
            name: $('#personnavn').val()
        };

        log('Verifying confirmation code');
        $('#bekreftelse').closest('.control-group').removeClass('error');

        $.post('/users/activate', person_data, function(res) {
            if (res.error != "") {
                log(res.error, 'error');
                if (res.error == 'invalid_code') {
                    $('#bekreftelse').closest('.control-group').addClass('error');
                }
            } else {
                log('Bruker bekreftet! La oss lage lånekort.', 'info');
                console.log(res);
                person_data['user_id'] = res.user_id;
                $('#slide4').hide();
                $('#slide5').show();
                //wait_for_blank_card();
            }
        });
    }

    function card_written(patron_id) {
        log('Lånkekort skrevet!', 'info')
        $('#slide5 p:last').append('<br />Gratulerer, kortet er klar til bruk!');
        lookup_patron_info(patron_id);
    }

    function showCollection(page) {
  //      $.get('/collection/items', function(res) {
    //    });
    }

    $(document).ready(function() {
        //$('#slide1').hide();
        $('.slide').hide();
        $('#slide1').show();

        $('#slide1 button').on('click', function(e) {
            $('.slide').hide();
            $('#slide10').show();
            $('.inner').removeClass('inner-small').addClass('inner-large')
        })

        $('#loan-btn').on('click', function (e) {
            e.preventDefault();
            $('#slide2').hide();
            $('#slide3').slideDown();
        });

        // Slide 3: Hent telefonnummer og navn

        var tlfnr = '';
        var notlfnr = $('#tlfnr').html();
        $('#slide3 .btn').on('click', function (e) {
            e.preventDefault();
            var chr = e.target.text.trim();
            if (chr == '←') {
                if (tlfnr.length > 0) {
                    tlfnr = tlfnr.substr(0, tlfnr.length-1);
                }
            } else if (chr == '✓') {
                lookup_person(tlfnr);
            } else {
                tlfnr += chr;
            }
            if (tlfnr.length == 0) {
                $('#slide3 h3').css('visibility','visible');
                $('#slide3 #number').css('visibility','hidden');
                $('#tlfnr').html(notlfnr);
            } else {
                $('#slide3 h3').css('visibility','hidden');
                $('#slide3 #number').css('visibility','visible');
                $('#tlfnr').html(tlfnr);
            }
        });

        // Slide 4: Bekreft navn og bekreftelseskode

        var bekreftelse = '';
        $('#bekreftelse-pad').hide();
        $('#bekreftelse').on('focus', function () {
            $('#bekreftelse-pad').show();
        });
        $('#bekreftelse-pad>div, #bekreftelse').on('click', function (e) {
            e.stopPropagation();
            //console.log('clicked pad');
        });
        $('body').on('click', function (e) {
            //console.log('clicked outside pad');
            $('#bekreftelse-pad').hide();
        });

        $('#slide4 .numpad .btn').on('click', function (e) {
            e.preventDefault();
            var chr = e.target.text;
            if (chr == '←') {
                if (bekreftelse.length > 0) {
                    bekreftelse = bekreftelse.substr(0, bekreftelse.length-1);
                }
            } else {
                bekreftelse += chr;
            }
            $('#bekreftelse').val(bekreftelse);
            $('#bekreftelse').focus();

        });

        $('#slide4 form').on('submit', function (e) {
            e.preventDefault();
            confirm_person();
        });

        $('#slide8 .btn').on('click', function (e) {
            var chr = e.target.text.trim(),
                bekr = $('#bekreftelse2').val();
            e.preventDefault();
            if (chr == '←') {
                if (bekr.length > 0) {
                    bekr = bekr.substr(0, bekr.length-1);
                }
            } else if (chr == '✓') {
                alert('Lagre eksisterende låner');
            } else {
                bekr += chr;
            }
            $('#bekreftelse2').val(bekr);
            $('#bekreftelse2').focus();

        });

        // Slide 5: Lag lånekort



    });

})(jQuery);
