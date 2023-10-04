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

Di default, l'admin è accessibile senza login. Per richiedere il login, fare due cose:

In `config/packages/security.yaml`, nodo `access_control`, aggiungere una regola per la route:

````yaml
access_control:
  - { path: ^/admin, roles: ROLE_ADMIN }
````

Aggiungere `IsGranted` alla dashboard (`src/Controller/Admin/DashboardController.php`):

````php
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
````


## Gestire le entity

Per svolgere operazioni CRUD su un'entity:

````shell
symfony console make:admin:crud
````

Selezionare l'entity da gestire, poi date sempre Invio.

Viene così generato `src/Controller/Admin/<entity>CrudController.php`.

Per renderlo raggiungibile, è necessario linkarlo in `src/Controller/Admin/LegacyFileCrudController.php`:

````php
public function configureMenuItems(): iterable
{
    yield MenuItem::linkToDashboard('Admin Home', 'fa fa-dashboard');
    yield MenuItem::linkToCrud('Files', 'fas fa-file', <entity>::class);
    yield MenuItem::linkToUrl('Home', 'fa fa-home', $this->generateUrl('app_homepage'));
}
````

Per trovare i nomini delle icone: [fontawesome](https://fontawesome.com/search?q=admin&o=r&m=free)

C'è anche `MenuItem::linkToRoute`, ma occhio che fa passare il rendering da EasyAdmin (l'URL è di EasyAdmin).


## Configurazione azioni su CRUD

La maggior parte delle funzioni si possono configurare:

1. a livello di dashboard ==> si applicano a tutti i CrudController
2. a livello di specifico CrudController

Ad es: `public function configureActions()` gestisce le azioni disponibili sulle row. Per modificarle:

````php
public function configureActions(): Actions
{
    return
        parent::configureActions()
            // add a link to the row detail page
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            // prevent delete
            ->disable(Action::DELETE);
````


## Configurare i campi

In `src/Controller/Admin/<entity>CrudController.php`:

````php
````

La lista completa dei tipi di field è: [Field Types](https://symfony.com/bundles/EasyAdminBundle/current/fields.html#field-types)

