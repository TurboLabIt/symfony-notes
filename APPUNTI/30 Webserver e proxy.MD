# Webserver e proxy

## 🛑 Attivazione HTTPS locale

Per creare la CA locale e attivare HTTPS:

`symfony server:ca:install`

## Impostazione proxy su OS

Per far funzionare il proxy, impostare `http://127.0.0.1:7080/proxy.pac`:

1. [Windows](https://www.dummies.com/computers/operating-systems/windows-10/how-to-set-up-a-proxy-in-windows-10/)
1. [Ubuntu](https://help.ubuntu.com/stable/ubuntu-help/net-proxy.html.en)
1. [Mac](https://support.apple.com/guide/mac-help/enter-proxy-server-settings-on-mac-mchlp2591/mac)

## Avvio proxy e webserver

🛑 Tutti i comandi devono essere eseguiti dalla cartella nella quale si trova il progetto.

🛑 **Chiudere Chrome PRIMA di avviare il proxy**, oppure riavviare Chrome con il proxy in esecuzione.

Attach del dominio .wip:

`symfony proxy:domain:attach my-domain`

Avvio del proxy:

`symfony proxy:start`

Visualizzazione dei domini disponili al proxy:

[http://127.0.0.1:7080](http://127.0.0.1:7080)

Avvio del server web:

`symfony server:start -d`
