# Creazione di una dashboard Admin con EasyAdmin

- 📚 [SymfonyCast](https://symfonycasts.com/screencast/easyadminbundle)
- 📚 [Symfony Doc](https://symfony.com/bundles/EasyAdminBundle/current/index.html)


## Installazione

````shell
symfony composer require admin
symfony make:admin:dashboard
````

Diviene disponibile subito `/admin`.


## Modificare lo slug di accesso all'admin

Per modificare lo slug:

````php
# src/Controller/Admin/DashboardController.php

#[Route('/nuovo-slug', name: 'admin')]
public function index(): Response
{
````

## Startup dev

Di default, il controller mostra la pagina di benvenuto di default tramite:

````php
#[Route('/admin', name: 'admin')]
public function index(): Response
{
    return parent::index();
````

Per iniziare lo sviluppo, mostrare il render del template:

````php
#[Route('/admin', name: 'admin')]
public function index(): Response
{
    //return parent::index();
    return $this->render('admin/index.html.twig');
````

Creare poi manualmente il template:

````php
# templates/admin/index.html.twig
{% extends '@EasyAdmin/page/content.html.twig' %}
````

## Richiedere login all'admin

````php
#[IsGranted('ROLE_ADMIN')]
#[Route('/admin', name: 'admin')]
public function index(): Response
{
````
