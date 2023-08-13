# Sistema di login

Il sistema si compone di:

- **authentication**: stabilire CHI SEI
- **authorization**: stabilire COSA PUOI FARE

firewall+authenticator_provider gestisce l'authentication, che carica i ruoli (ROLE_REGISTERED, ROLE_MODERATOR, ROLE_ADMIN).

access_control stabilice quali route sono riservate a certi ruoli.


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

## Creare gli utenti senza form di registrazione

Inserire manualmente le righe a DB:

- `roles`: `["ROLE_ADMIN"]`
- `password`: generare l'hash con il comando `symfony console security:hash`


## Creare il sistema di login

`symfony console make:auth`

Rispondere:

- `Login form authenticator`
- `LoginFormAuthenticator`

Questo crea:

1. Controller per login/logout - `src/Controller/SecurityController.php`
1. HTML del form di login - `templates/security/login.html.twig`
1. Autenticatore - `src/Security/LoginFormAuthenticator.php`


## Stabilire quali route sono protette da login

In `config/packages/security.yaml`, aggiungere elementi a `access_control` per stabilire quali 
route richiedono particolari ruoli.

