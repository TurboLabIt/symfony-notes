# Autowiring

Mettendo una varibile con typehint nella firma della funzione, Symfony passa un oggetto del tipo specificato:

```
public function toggleArticleHeart($articleId, LoggerInterface $logger)
{
    $logger->info($articleId . " got an heart");
    ...
}
```

Autowiring funziona solo con:
- action nei controller
- __construct dei service


## Lista nomi

Per visualizzare i nomi dei servizi disponibili per l'autowiring:

`symfony console debug:autowiring`
