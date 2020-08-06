# Nuovo progetto da zero

## 🛑  Creare il repository 

[Creare il repository](https://github.com/new) prima di iniziare

Clonare in locale:

````
git clone git@github.com:ZaneCEO/test-private.git
git checkout -b dev
git push
````


## Creare il nuovo progetto Symfony

Creazione del progetto (🛑 fuori dal repo clonato)

````
symfony new my_project_name --version=4.4
cd my_project_name
````

.gitignore completo:

`curl -O https://raw.githubusercontent.com/ZaneCEO/webdev-gitignore/master/.gitignore`

Aggiunta bundle minimi:

`composer require annotations && composer require twig && composer require asset && composer require doctrine && composer require maker && composer require debug --dev && composer require profiler --dev`

Indichiamo esplicitamente quale versione di PHP usare:

`echo "7.3" > .php-version`


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
