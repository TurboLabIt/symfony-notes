
````
symfony composer require encore
yarn
````

node ha due package manager: yarn e npm.

## webpack.config.js

E' il file di configurazione generale di webpack


## assets/app.js

E'  la lista di import dalla quale viene generato il file finale.

Per importare un altro file:

````
import trackingGtmWizardMap from "../../../tracking/gtm-wizard-map";
trackingGtmWizardMap(); //esegue la funzione "default"
 ````

Il percorso è relativo al file `app.js` stesso


## Moduli

Ogni file importato è sostanzialmente una funzione:

````
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

````
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
 

## Aggiungere jQuery

[yarnpkg.com](https://yarnpkg.com/)

`yarn add jquery --dev`

In `app.js`:

`import $ from 'jquery'`

Attenzione: `$` non sarà comunque disponibile nel contesto globale.


## Aggiungere Bootstrap

[yarnpkg.com](https://yarnpkg.com/)

`yarn add bootstrap --dev`

In `app.js`:

`import 'bootstrap'`

Poiché Bootstrap è un plugin di jQuery, non è necessario specificare il percorso del .js


## In Twig

Importazione degli asset nei template Twig

- CSS: `{{ encore_entry_link_tags('app) }}`
- JS: `{{ encore_entry_script_tags('app) }}`


## Comandi di build

- In dev: `yarn watch`
- Per prod: `yarn build`

Lista completa in `package.json`
