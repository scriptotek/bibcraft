BIBCRAFT
============

Bibcraft er Realfagsbibliotekets eksperimentelle og mobile utlånssystem.
Bygget på Laravel og AngularJS.

Oppsett:

    composer install
    php artisan migrate

Hvilken samling som vises på selvbetjeningsautomaten settes ved å opprette filen `public/config.json`:

    {
        "activeCollection": 2
    }

Du kan evt. kopiere `public/config.dist.json`
