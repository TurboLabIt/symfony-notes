# Creazione di una dashboard Admin con EasyAdmin

- 📚 [SymfonyCast](https://symfonycasts.com/screencast/easyadminbundle)
- 📚 [Symfony Doc](https://symfony.com/bundles/EasyAdminBundle/current/index.html)


## Installazione

````shell
symfony composer require admin
symfony make:admin:dashboard
````

Diviene disponibile subito `/admin`. Per modificare lo slug:

````php
# Controller/Admin/DashboardController.php

#[Route('/nuovo-slug', name: 'admin')]
public function index(): Response
{
````
