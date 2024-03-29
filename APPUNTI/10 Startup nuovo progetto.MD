# Nuovo progetto da zero

## 🛑  Creare il repository 

[Creare il repository](https://github.com/new) prima di iniziare

Clonare in locale:

````
git clone git@github.com:ZaneCEO/test-private.git
git checkout -b dev
git push --set-upstream origin dev
````


## Creare il nuovo progetto Symfony

Creazione del progetto (🛑 fuori dal repo clonato)

````
symfony new my_project_name --version=4.4
cd my_project_name
````

.gitignore completo:

`curl -O https://raw.githubusercontent.com/ZaneCEO/webdev-gitignore/master/.gitignore`


## Versione di PHP

Indichiamo esplicitamente quale versione di PHP usare:

````
symfony local:php:list
echo "8.0" > .php-version
````

In `composer.json`:

````
"require": {
    "php": ">=8.0",
````

## Bundle

Aggiunta bundle minimi:

`symfony composer require annotations twig cache asset doctrine mailer stof/doctrine-extensions-bundle && symfony composer require maker debug profiler --dev`

In `app/config/packages/stof_doctrine_extensions.yml`:

````yaml
stof_doctrine_extensions:
   orm:
      default:
          timestampable: true
````



## Spostare Symfony nel repo

🛑 Eliminare il .git di Symfony:

`rm -rf .git`

Spostare tutto:

````
shopt -s dotglob
mv * ../test-private
````

## Panoramica cartelle

- 🌎 **public**: webroot con index.php (front-controller)
- **config**: file di configurazione
- 🧑🏻‍💻 **src**: posizionare i file php qui!
- **templates**: twig
- **var**:
- **vendor**: motore Symfony, bundle e altri pacchetti scaricati con composer
