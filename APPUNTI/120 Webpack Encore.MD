# Webpack Encore

node library per gestione JS e CSS. 

* [Webpack](https://webpack.js.org/) è il nome del tool - è uno *static module bundler*
* [Encore](https://symfony.com/doc/current/frontend.html) è un bundle Symfony per l'integrazione di Webpack 


# Installazione nodejs

Prendere l'ultima versione [da qui](https://github.com/nodesource/distributions/blob/master/README.md#debinstall)

Eseguire [webstackup/script/node.js/install.sh](https://github.com/TurboLabIt/webstackup/blob/master/script/node.js/install.sh)

📚 Vedi anche: [Come installare Node.js e npm su Ubuntu](https://turbolab.it/3724)


# Installazione yarn

nodejs ha due package manager: 

* npm - installato automaticamente da nodejs
* yarn - da installare a mano

Per installare yarn: i comandi vengono mostrati alla fine dell'installazione di nodejs. Se hai usato 
[webstackup/script/node.js/install.sh](https://github.com/TurboLabIt/webstackup/blob/master/script/node.js/install.sh),
yarn è già installato

📚 Vedi anche: [Come usare Node.js, npm e yarn sul PC](https://turbolab.it/3726)


# Installazione Webpack Encore

````shell
symfony composer require encore
yarn install
````

L'installazione crea *package.json*: è il file che viene letto da yarn.

Scommentare le entry in `templates/base.html.twig`:

* `{{ encore_entry_link_tags('app') }}`: include il CSS
* `{{ encore_entry_script_tags('app') }}`: include il JS

# Comandi yarn

* `yarn install`: scarica i pacchetti in `node_modules` (equivalente al `vendor` di composer)
* ✨ `yarn watch`

I comandi yarn sono definiti in `package.json`.


# webpack.config.js

E' il file di configurazione principale di webpack.

`.addEntry('app', './assets/app.js')`:

1. `app`: è il nome del file pubblico generato
1. `./assets/app.js`: è il file che deve essere letto


# assets/app.js

E'  la lista di import dalla quale viene generato il file finale.

Per importare un altro file:

````javascript
import trackingGtmWizardMap from "../../../tracking/gtm-wizard-map";
trackingGtmWizardMap(); //esegue la funzione "default"
 ````

Il percorso è relativo al file `app.js` stesso


## Moduli

Ogni file importato è sostanzialmente una funzione:

````javascript
import TrackingGtmBase from "./gtm-base";

export default function()
{
    var trackPlace      = document.querySelector('#track-place-name');
    var trackSport      = document.querySelector('#track-sport-name');
    var trackDateFrom   = document.querySelector('.track.date-from');
    var trackDateTo     = document.querySelector('.track.date-to');

    if(
        trackPlace && trackSport &&
        trackDateFrom && trackDateTo
    ) {
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            event   : 'search_started',
            place   : TrackingGtmBase.buildPlaceGtm(trackPlace),
            sport   : trackSport.innerText.trim(),
            date    : TrackingGtmBase.buildDateGtm(trackDateFrom, trackDateTo)
        });
    }
}
````

La prima direttiva importa un oggetto esterno che contiene i metodi di supporto:

````javascript
export default {

    buildPlaceGtm: function(trackPlace) {
        var trackPlacePieces = trackPlace.innerText.split(",");
        var trackPlaceGtm =
            trackPlacePieces[0].trim() + "_" +
            trackPlacePieces[1].trim() + ",_" +
            trackPlacePieces[2].trim();

        return trackPlaceGtm;
    },

    buildDateGtm: function(trackDateFrom, trackDateTo) {
        var trackDateGtm =
            trackDateFrom.innerText.trim() + "_" +
            trackDateTo.innerText.trim();

        return trackDateGtm;
    }
}

````

## Supporto SASS

`yarn add sass-loader sass --dev`

Nel file `webpack.config.js`, scommentare `enableSassLoader`.
 

## Aggiungere jQuery

[yarnpkg.com](https://yarnpkg.com/package/jquery)

`yarn add jquery@latest --dev`

In `app.js`:

`import $ from 'jquery'`

Attenzione: `$` non sarà comunque disponibile nel contesto globale.
Se serve per motivi legacy:

`global.$ = $;`


## Aggiungere Bootstrap

[yarnpkg.com](https://yarnpkg.com/package/bootstrap)

`yarn add popper.js --dev && yarn add bootstrap@latest --dev`

La prima è una *peer dependency* per Bootstrap, ovvero va 
importata a mano.

In `app.js`:

`import 'bootstrap'`

Poiché Bootstrap è un plugin di jQuery, non è necessario specificare il percorso del .js. 
Questo funziona perché i plugin modificano jQuery (lo importano, e quando due moduli
importano lo stesso oggetto, ottengono la stessa istanza).


## Aggiungere Fontawesome

[yarnpkg.com](https://yarnpkg.com/package/@fortawesome/fontawesome-free)

`yarn add @fortawesome/fontawesome-free@latest --dev`


## Spostare le immagini fuori da public

Spostare manualmente le immagini da `public/images` a `assets/images`.

Installare:

````
yarn add file-loader --dev
````

Abilitare la copia in webpack.config.js:

````javascript
.copyFiles({
    from: './assets/images',
    to: 'images/[path][name].[hash:8].[ext]'
})

.copyFiles({
    from: './assets/images/',
    to: 'images/[path][name].[ext]',
    pattern: /favicon\.ico$/
})
````
