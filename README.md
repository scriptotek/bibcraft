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

