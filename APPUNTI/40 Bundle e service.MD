# Comandi debugging

Ogni oggetto è un **service**. I service sono disponibili tramite il **container**.

I **bundle** istanziano i servizi e li inseriscono nel container.

Esempio:

*Monolog bundle* istanzia *logger service*

I bundle vengono registrati in `config/bundles.php`. Generalmente lo fa composer, tramite il componente "Symfony Flex" (plugin per composer) e il sistema delle recipies.

https://flex.symfony.com/


## Installare un bundle

`symfony composer require security-checker --dev`

I bundle che vengono usati solo nell'ambiente dev, ma non servono in produzione, vanno installati con `--dev`.

Dopo l'installazione, verificare se è stato creato un nuovo `.yaml` sotto `config/packages/`.

## Rimuovere un bundle

`symfony composer remove security-checker`


## Visualizzare bundle installati:

`symfony console config:dump`

Per vedere le opzioni di configurazione di un determinato bundle (valori di default)

`symfony console config:dump nomebundle`

Per vedere le opzioni di configurazione ATTUALI di un determinato bundle

`symfony console debug:config nomebundle`


## Visualizzazione servizi

Tutti i servizi disponibili:

`symfony console debug:container -show-private`

Filtrare la lista dei servizi disponibili

`symfony console debug:container -show-private log`


## Configurazione dei servizi

Per modificare le opzioni di config di un bundle

1. config/packages/nomequalisasi.yaml
2. copiare struttura sputata da config:dump

Esempio:

```
knp_markdown:
  parser:
    service: markdown.parser.light
```

## Visualizzare le recipes in uso

`symfony composer recipes`


## Pack

Alcuni bundle sono "pack" => fanno `composer require` di molteplici bundle con un solo comando. Aggiungo entry distinte in `composer.json` per ognuno, come se fossero stati installati uno per volta.