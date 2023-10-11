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
public function configureFields(string $pageName): iterable
{
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

    yield IntegerField::new('visite')
            /*->formatValue(function($value, LegacyFile $entity) {

                $formattedValue = number_format($value, "0", ",", ".");
                return $formattedValue;
            })*/
            ->setTemplatePath('admin/field/downloads.html.twig')
            ->hideWhenCreating()
            ->setTextAlign("right")
            ->setDisabled();

    yield TextField::new('formato')
            ->hideWhenCreating()
            ->setTextAlign("right")
            ->setDisabled();

    $entity = $this->getContext()->getEntity()->getInstance();
    $that   = $this;

    yield ImageField::new('uploadedFile')
            ->setBasePath('scarica/')
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
}
````

La lista completa dei tipi di field è: [Field Types](https://symfony.com/bundles/EasyAdminBundle/current/fields.html#field-types)


## Template Twig personalizzati per i campi

Per personalizzare il template utilizzato per mostrare un campo nella tabella (index) e nel dettaglio (detail), si usa `setTemplatePath()`:

````php
yield TextField::new("url")
        ->setTemplatePath('admin/field/download-link.html.twig')
        ->hideOnForm();

yield ImageField::new('url', 'Anteprima')
    ->formatValue(function($value, LegacyFile $entity) {

        if( !$entity->downloadableExists() ) {
           return '/images/error.png';
        }

        if( $entity->isImage() ) {
            return $value;
        }

        $formattedValue =
            match($entity->getFormato()) {
                'pdf'           => '/images/pdf.png',
                default         => '/images/question-mark.png'
            };

        return $formattedValue;
    })
    ->setTemplatePath('admin/field/image.html.twig')
    ->hideOnForm();
````

Non è possibile definire variabili Twig come si fa dai controller Symfony normali, ma i template hanno accesso alle entity:


````
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
public function __construct(protected EntityManagerInterface $em, protected Environment $twig, ContainerBagInterface $parameterBag)
{
    $averageDownloadCount = $em->getRepository(LegacyFile::class)->getAverageDownloadCount();
    LegacyFile::setAverageDownloadCount($averageDownloadCount);
}


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

````
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




## Campo upload file

Definire (dove vuoi, forse nell'entity (??)) il percorso (relativo al progetto) in cui salvare il file caricati:

````php
const DOWNLOADABLES_DIRECTORY = "assets" . DIRECTORY_SEPARATOR . "downloadables" . DIRECTORY_SEPARATOR;
````

Definire un metodo nell'entity che gestisca solo il nome del file su filesystem:

````php
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
````

Definire un metodo nell'entity che ritorni l'URL pubblico del file:

````php
public function getUrl() : string
{
    return sprintf('/scarica/%s', $this->getId());
}
````

Nel CrudController, aggiungere il campo:

````php
public function __construct(protected EntityManagerInterface $em)
{ }


public function configureFields(string $pageName): iterable
{
    // ...

    $entity = $this->getContext()->getEntity()->getInstance();
    $that   = $this;

    yield ImageField::new('uploadedFile')
            // URL slug(s)
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
            ->formatValue(function($value, LegacyFile $entity) use($that) {

                $fileFullPath = $that->getFileFullPath($entity);

                $text = "<a href=\"$value\">";
                $text .=
                    !file_exists($fileFullPath) || !is_file($fileFullPath) || !is_readable($fileFullPath)
                        ? "🛑 FILE ERROR" : "Download";

                $text .= "</a>";

                return $text;
            })
            ->hideOnForm();
}
````

Con questa configurazione, il file cancellato da filesystem solo quando l'utente modifica/trash lo specifico campo.

Per eliminare il file quando viene eliminata l'entity:

````php
public function __construct(protected ContainerBagInterface $parameterBag)
{ }


public function delete(AdminContext $context) : KeyValueStore|Response
{
    $entity = $context->getEntity()->getInstance();
    $fileFullPath = $this->getFileFullPath($entity);

    $response = parent::delete($context);

    if( file_exists($fileFullPath) && is_file($fileFullPath) && is_writable($fileFullPath) ) {
        unlink($fileFullPath);
    }

    return $response;
}


protected function getDownloadablesDir() : string
{
    $dir = $this->parameterBag->get('kernel.project_dir') . DIRECTORY_SEPARATOR . LegacyFile::DOWNLOADABLES_DIRECTORY;
    return $dir;
}


protected function getFileFullPath(LegacyFile $entity) : ?string
{
    $fileName = $entity->getUploadedFile();
    if( empty($fileName) ) {
        return null;
    }

    $fileFullPath = $this->getDownloadablesDir() . $fileName;
    return $fileFullPath;
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
