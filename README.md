ebok-i-bokhylla
============

Et lite prosjekt for å lage enkle, utskrivbare presentasjonssider av e-bøker for plassering i bokhylla.
Henter inn metadata fra Bibsys og Library of Congress, som kan redigeres i etterkant.

Demo på [ebok.biblionaut.net](http://ebok.biblionaut.net)

Oppsett:

    composer install
    php artisan migrate
    mkdir app/storage/covers
    sudo chown -R www-data:www-data app/storage
    sudo chown -R www-data:www-data app/database 
    sudo pecl install pecl_http

For PDF-eksport har jeg prøvd ut wkhtmltopdf. Siden wkhtmltopdf bygger på 
webkit skulle man forvente rendringer av samme kvalitet som de man får fra
Chrome, men dette har av en eller annen grunn ikke vært tilfellet.

wkhtmltopdf krever i praksis en X-server. Det er riktignok mulig å kjøre uten, 
men da får man fort en [seg fault](https://code.google.com/p/wkhtmltopdf/issues/detail?id=786).. 
En enkel «hodeløs» X-server er xvfb. Shell-scriptet jeg bruker for å kjøre wkhtmltopdf
med xvfb ser slik ut:

    #!/bin/sh
    xvfb-run --server-args="-screen 0, 1024x680x24" /usr/local/bin/wkhtmltopdf-i386 --use-xserver "$@"

