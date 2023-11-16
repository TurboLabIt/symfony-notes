Run code async (queue e worker)

- 📚 [SymfonyCast](https://symfonycasts.com/screencast/messenger/)
- 📚 [Symfony docs](https://symfony.com/doc/current/messenger.html)

Il sistema si divide in due file:

- **Message**:
- **MessageHandler**:


## Installazione

````shell
symfony composer require messenger
````


## Creazione del Message

````shell
mkdir src/Message/
````

Creare una nuova classe "vuota". Ad esempio:

````php
<?php
namespace App\Message;


class EmailSendOfferListing
{
    public function __construct(protected int $opportunityId)
    { }


    public function getOpportunityId() : int
    {
        return $this->opportunityId;
    }
}
````

⚠ Questa classe viene serializzata quando si usa l'invio async!
⚠ Non può quindi contenere servizi o risorse, ma solo dati.


## Creazione del MessageHandler

````shell
mkdir src/MessageHandler/
````

Creare una nuova classe. Il nome non è importante. E' invece importante che:

- ogni metodo che gestisce un messaggio abbia l'attributo `#[AsMessageHandler]`
- ogni metodo che gestisce un messaggio abbia un solo parametro, del tipo uguale al messaggio che deve gestire

Ad esempio:

````php
<?php
namespace App\MessageHandler;

use App\Entity\Opportunity;
use App\Message\EmailSendOfferListing;
use App\Message\EmailSendOfferSelected;
use App\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;


class EmailHandler
{
    public function __construct(protected Mailer $mailer, protected EntityManagerInterface $em)
    { }


    #[AsMessageHandler]
    public function sendOfferListing(EmailSendOfferListing $message)
    {
        $opportunity = $this->getOpportunityFromMessage($message);
        $this->mailer->sendOfferListingIfElegible($opportunity);
    }


    #[AsMessageHandler]
    public function sendOfferSelected(EmailSendOfferSelected $message)
    {
        $opportunity    = $this->getOpportunityFromMessage($message);
        $offer          = $message->getOffer();
        $this->mailer->sendOfferSelectedIfElegible($opportunity, $offer);
    }


    protected function getOpportunityFromMessage($message) : Opportunity
    {
        $opportunityId  = $message->getOpportunityId();
        $opportunity    = $this->em->getRepository(Opportunity::class)->find($opportunityId);
        return $opportunity;
    }
}
````

L'handler può avere tutti i servizi e le risorse che gli servono (non viene serializzato).


## Vedere quale Handler gestisce quale Message

````shell
symfony console debug:messenger
````


## Async, Fase 1 | Queue dei messaggi

Attivare `MESSENGER_TRANSPORT_DSN=doctrine://` in `.env`.

In `config/packages/messenger.yaml`:

- attivare il trasporto `async: '%env(MESSENGER_TRANSPORT_DSN)%'`
- indicare quali messaggi gestire in modo async

  ````yaml
  framework:
    messenger:
        transports:
            async: '%env(MESSENGER_TRANSPORT_DSN)%'
        routing:
            'App\Message\*': async
  ````

Verificare che non ci siano migration da fare:

````shell
symfony console make:migration
````

Se il db è allineato, installare il transport per Doctrine e creare la tabella:

````shell
symfony composer require symfony/doctrine-messenger && symfony console make:migration && symfony console doctrine:migrations:migrate --no-interaction
````

Da qui in poi, i messaggi dispatchati vengono serializzati e aggiunti alla coda, ma non più processati automaticamente.


## Async, Fase 2 | Worker per l'elaborazione dei messaggi nella queue 


