# Creazione di una dashboard Admin con EasyAdmin

🛑 Prima di implementare l'admin, serve il [sistema di login](https://github.com/TurboLabIt/symfony-notes/blob/master/APPUNTI/75%20Login.md)

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

Di default, l'admin è accessibile senza login. Per richiedere il login:

````php
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends
````

````yaml
# config/packages/security.yaml
access_control:
  - { path: ^/admin, roles: ROLE_ADMIN }
````
