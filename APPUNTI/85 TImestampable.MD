https://symfony.com/doc/current/bundles/StofDoctrineExtensionsBundle/installation.html

`symfony composer require stof/doctrine-extensions-bundle`

In `app/config/config.yml`:
````
stof_doctrine_extensions:
   orm:
      default:
          timestampable: true
````

`use TimestampableEntity;`