# Sistema di login

## Require dei pacchetti necessari

`symfony composer require symfony/maker-bundle --dev &&  symfony composer require twig orm symfony/security-bundle`

## Creare utente

🛑 `User` è una entity, ma si crea tramite un comando dedicato che fa anche degli altri mestieri (es: aggiorna `config/security.yaml`):

`symfony console make:user`

L'entity creata `implements UserInterface`.

Può essere modificata come qualsiasi altra entity.

C'è sempre un metodo `getUserIdentifier()`, che però serve solo per visualizzare il nome dell'utente loggato => non server per l'auth.

## Modifica all'utente

Meglio fare gli ID "unsigned":

````php
#[ORM\Id]
#[ORM\GeneratedValue]
#[ORM\Column(type: "smallint", options: [ "unsigned" => true])]
private ?int $id = null;
````

Timestampare gli utenti:

````php
use TimestampableEntity;
````

## Creare il sistema di login

`symfony console make:auth`

Questo crea:

1. Controller per login/logout - `src/Controller/SecurityController.php`
1. HTML del form di login - `templates/security/login.html.twig`
1. Autenticatore - `src/Security/AdminAuthenticator.php`

Se già non c'è, modificare `config/security.xml` e aggiungere

````
security:
    enable_authenticator_manager: true
````
