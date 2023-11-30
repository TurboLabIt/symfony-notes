# Upgrade del framework

- 📚 [Upgrading a Minor Version](https://symfony.com/doc/current/setup/upgrade_minor.html)
- 📚 [Upgrading a Major Version](https://symfony.com/doc/current/setup/upgrade_major.html)

Prima di passare alla major successiva è importante l'ugrade all'ultima minor corrente, di modo da 
generare messaggi parlanti relativi alle *deprecation* e non errori generici.


## 1. Versione in composer.json

*Find and replace* della versione X.y con la nuova versione.


## 2. composer

`symfony composer update`


## 3. installare Rector

`symfony composer require rector/rector --dev && vendor/bin/rector`


## 4. Eseguire Rector

`vendor/bin/rector process --dry-run`

Questo visualizza solo le modifiche. Per applicarle automaticamente, ri-lanciare senza `--dry-run`.


## 5. Upgrade delle Recipes di Flex

`symfony composer recipes:update`

E' necessario aggiornarle una alla volta
