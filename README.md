BIBCRAFT
============

Bibcraft er Realfagsbibliotekets eksperimentelle og mobile utlånssystem.
Bygget på Laravel og AngularJS.

Oppsett:

	cd client
    composer install
    php artisan migrate

    cd ../node-server
    npm install

Hvilken samling som vises på selvbetjeningsautomaten settes ved å opprette filen `public/config.json`:

    {
        "activeCollection": 2
    }

Du kan evt. kopiere `public/config.dist.json`.

Webserveren må ha skrivetilgang til `public/covers` og `app/storage`.

