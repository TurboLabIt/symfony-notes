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

Di default, il controller mostra la pagina di benvenuto tramite:

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


## Gestire le entity tramite CrudController

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

Per evitare che le action vengano nascoste all'interno del menu e mostrarle immediatamente:

````php
public function configureCrud(): Crud
{
    return
        parent::configureCrud()
            ->showEntityActionsInlined();
````


## Configurare i campi

In `src/Controller/Admin/<entity>CrudController.php`:

````php
public function configureFields(string $pageName): iterable
{
    $entity = $this->getContext()->getEntity()->getInstance();
    $that   = $this;

    yield IdField::new('id')
            ->hideWhenCreating()
            ->setDisabled();

    yield TextField::new('titolo');

    yield IntegerField::new('data_creazione')
            ->formatValue(static function($value, LegacyFile $file){
                return empty($value) ? $value : \DateTime::createFromFormat('YmdHis', $value)->format("d F Y");
            })
            ->hideOnForm()
            ->setDisabled();

    yield TextField::new('formato')
            ->hideWhenCreating()
            ->setTextAlign("right")
            ->setDisabled();
````

La lista completa dei tipi di field è: [Field Types](https://symfony.com/bundles/EasyAdminBundle/current/fields.html#field-types)


## Template Twig personalizzati per i campi

Per personalizzare il template utilizzato per mostrare un campo nella tabella (index) e nel dettaglio (detail), si usa `setTemplatePath()`:

````php
yield TextField::new("url")
        ->setTemplatePath('admin/field/download-link.html.twig')
        ->hideOnForm();
````

Non è possibile definire variabili Twig come si fa dai controller Symfony normali, ma i template hanno accesso alle entity:

````twig
{# @var ea \EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext #}
{# @var field \EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto #}
{# @var entity \EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto #}

<a href="{{ field.value }}">
    {% if entity.instance.downloadableExists %}Download{% else %}🛑 FILE ERROR{% endif %}
</a>
````

In alternativa, se è necessario rendere disponibile al template una variabile che non fa parte della entity,
la si può assegnare al contesto globale di Twig:

````php
public function configureFields(string $pageName): iterable
{
    if( !in_array($pageName, [Crud::PAGE_NEW, Crud::PAGE_EDIT]) ) {

        $averageDownloadCount = $this->em->getRepository(LegacyFile::class)->getAverageDownloadCount();
        $this->twig->addGlobal("averageDownloadCount", $averageDownloadCount);
    }

    yield IntegerField::new('visite')
            ->setTemplatePath('admin/field/downloads.html.twig')
            ->hideWhenCreating()
            ->setTextAlign("right")
            ->setDisabled();
}
````

````twig
{# @var ea \EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext #}
{# @var field \EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto #}
{# @var entity \EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto #}
{# @var averageDownloadCount int #}

{% if field.value > 0 and field.value < averageDownloadCount/3 %}
    <span title="Molto peggio della media di {{ averageDownloadCount|number_format(0, '', '.') }}">🧻</span>
{% endif %}

{% if field.value > averageDownloadCount*3 %}
    <span title="Molto meglio della media di {{ averageDownloadCount|number_format(0, '', '.') }}">🎉</span>
{% endif %}

{% if field.value > averageDownloadCount*10 %}
    <span title="Più di 10x rispetto alla media!">🥇</span>
{% endif %}

{{ field.formattedValue|number_format(0, '', '.') }}
````

## Evitare che un ImageType sia cliccabile

Di default, il campo ImageType mostra nell'index un'anteprima dell'immagine che, se cliccata, apre un lightbox
con l'immagine zoomata.

Per evitarlo:

````php
yield ImageField::new('url', 'Anteprima')
    ->setTemplatePath('admin/field/image.html.twig');
````

````twig
{# @var ea \EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext #}
{# @var field \EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto #}
{# @var entity \EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto #}

{% set isClickable = entity.instance.downloadableExists and entity.instance.isImage %}

{% set images = field.formattedValue %}
{% if images is not iterable %}
    {% set images = [images] %}
{% endif %}

{% for image in images %}

    {% set html_id = 'ea-lightbox-' ~ field.uniqueId ~ '-' ~ loop.index %}

    {% if isClickable %}
        <a href="#" class="ea-lightbox-thumbnail" data-ea-lightbox-content-selector="#{{ html_id }}">
    {% endif %}

    <img
        src="{{ asset(image) }}" class="img-fluid"
        {% if not isClickable %} style="max-height: 50px; max-width: 100px;"{% endif %}
        {# https://github.com/mdn/sprints/issues/4014 #}
        onerror="this.onerror=null; this.src='/images/error.png'"
    >

    {% if isClickable %}

        </a>

        <div id="{{ html_id }}" class="ea-lightbox">
            <img src="{{ asset(image) }}">
        </div>

    {% endif %}

{% endfor %}
````


## Campo upload file

Aggiungere il necessario all'entity:

````php
<?php
class LegacyFile
{
    const DOWNLOADABLES_DIRECTORY = "assets" . DIRECTORY_SEPARATOR . "downloadables" . DIRECTORY_SEPARATOR;

    protected static string $projectDir;

    public static function setProjectDir(string $projectDir) : void
    {
        $projectDir = rtrim($projectDir, '/') . '/';
        static::$projectDir = $projectDir;
    }

    public function getUploadedFile() : ?string
    {
        if( empty($this->getId()) || empty($this->getFormato()) ) {
            return null;
        }

        $fileName = $this->getId() . "." . $this->getFormato();
        return $fileName;
    }

    public function setUploadedFile(?string $tempFileName) : static
    {
        return $this;
    }

    public function getDownloadablesDir() : string
    {
        if( empty(static::$projectDir) ) {
            throw new ConfigurationException("Project Dir not set!");
        }

        return static::$projectDir . static::DOWNLOADABLES_DIRECTORY;
    }

    public function getFileFullPath() : ?string
    {
        $fileName = $this->getUploadedFile();
        if( empty($fileName) ) {
            return null;
        }

        $fileFullPath = $this->getDownloadablesDir() . $fileName;
        return $fileFullPath;
    }

    public function downloadableExists() : bool
    {
        $downloadableFullPath = $this->getFileFullPath();

        $downloadableExists =
            !empty($downloadableFullPath) && file_exists($downloadableFullPath) &&
            is_file($downloadableFullPath) && is_readable($downloadableFullPath);

        return $downloadableExists;
    }

    public function getUrl() : string
    {
        return sprintf('/scarica/%s', $this->getId());
    }
}
````

Richiamare le funzioni dell'entity nel CrudController:

````php
public function __construct(protected EntityManagerInterface $em, protected Environment $twig, ContainerBagInterface $parameterBag)
{
    $projectDir = $parameterBag->get('kernel.project_dir');
    LegacyFile::setProjectDir($projectDir);
}

public function configureFields(string $pageName): iterable
{
    $entity = $this->getContext()->getEntity()->getInstance();
    $that   = $this;

    yield ImageField::new('uploadedFile')
            // URL slug(s) prefix
            ->setBasePath('scarica/')
            // path on filesystem (relative to Symfony project)
            ->setUploadDir('assets/downloadables')
            ->setUploadedFileNamePattern('[uuid]')
            ->setFormTypeOption('upload_new', function (UploadedFile $file, string $uploadDir, string $fileName) use($that, $entity) {

                $fileExtension = $file->guessExtension();
                $entity->setFormato($fileExtension);

                $that->em->persist($entity);
                $this->em->flush();

                $finalFileName = $entity->getUploadedFile();
                $file->move($uploadDir, $finalFileName);
            })
            ->setRequired($pageName !== Crud::PAGE_EDIT )
            ->hideOnDetail()
            ->hideOnIndex();

    yield TextField::new("url")
            ->setTemplatePath('admin/field/download-link.html.twig')
            ->hideOnForm();
````

Con questa configurazione, il file viene cancellato da filesystem solo quando l'utente modifica/trash lo specifico campo.

Per eliminare il file quando viene eliminata l'entity, aggiungere nel CrudController anche:

````php
public function delete(AdminContext $context) : KeyValueStore|Response
{
    $entity = $context->getEntity()->getInstance();
    $fileFullPath = $entity->getFileFullPath();
    $downloadableFileExists = $entity->downloadableExists();

    $response = parent::delete($context);

    if($downloadableFileExists) {
        unlink($fileFullPath);
    }

    return $response;
}
````


## Paginazione e numero risultati listato:

In dashobard oppure CrudController:

````php
public function configureCrud(): Crud
{
    return
        parent::configureCrud()
            ->setPaginatorPageSize(5000)
            ->setDefaultSort(['id' => 'DESC']);
}
````

## Aprire una pagina specifica al login

Se non hai niente da mostrare nella dashboard, puoi aprire un'altra pagina al login tramite redirect. In `DashboardController.php`:

````php
public function __construct(protected AdminUrlGenerator $adminUrlGenerator)
{ }


#[Route('/admin', name: 'admin')]
public function index(): Response
{
    return $this->redirect($this->adminUrlGenerator->setController(LegacyFileCrudController::class)->generateUrl());
}
````
