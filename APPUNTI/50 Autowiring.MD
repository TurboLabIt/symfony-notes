# Autowiring

Mettendo una varibile con typehint nella firma della funzione, Symfony passa un oggetto del tipo specificato:

```php
public function toggleArticleHeart($articleId, LoggerInterface $logger)
{
    $logger->info($articleId . " got an heart");
}
```

Autowiring funziona solo con:

* action nei controller
* `__construct` dei service


## Trovare servizi

Per visualizzare i nomi dei servizi disponibili per l'autowiring:

`symfony console debug:autowiring`

Per trovare il servizio che eroga una determinata funzionalità:

`symfony console debug:autowiring log`