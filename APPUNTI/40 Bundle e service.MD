# Comandi debugging

Ogni oggetto è un **service**. I service sono disponibili tramite il **container**.

I **bundle** istanziano i servizi e li inseriscono nel container.

Esempio:

*Monolog bundle* istanzia *logger service*

I bundle vengono registrati in `config/bundles.php` (generalmente lo fa composer).


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

