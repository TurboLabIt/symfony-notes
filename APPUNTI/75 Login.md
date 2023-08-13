# Sistema di login

Il sistema si compone di:

- **authentication**: stabilire CHI SEI
- **authorization**: stabilire COSA PUOI FARE

L'implementazione:

- **firewall+authenticator_provider**: gestisce l'authentication, che *carica* i ruoli (ROLE_USER, ROLE_ADMIN)
- **access_control o IsGranted()**: rende accessibili determinate route solo se l'utente *ha* un deleterminato ruolo


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
route richiedono particolari ruoli:

````php
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends
````

In alternativa (ma per l'admin è necessario metterli entrambi):

````yaml
# config/packages/security.yaml
access_control:
  - { path: ^/admin, roles: ROLE_ADMIN }
````

L'utente loggato ha sempre `ROLE_USER`, mentre `ROLE_ADMIN` va assegnato puntualmente.

In alternativa, ci sono attributi come:

- `PUBLIC_ACCESS`: disponibile anche senza login
- `IS_AUTHENTICATED` / `IS_AUTHENTICATED_REMEMBERED`: uguale a `ROLE_USER`
- `IS_AUTHENTICATED_FULLY`: solo se l'utente ha fatto login esplicitamente, no "remember me"


## Controllare se l'utente è loggato nel template twig:

````twig
{% if is_granted('ROLE_USER') %}
````
