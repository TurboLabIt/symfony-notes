# Gestione flash messages

In controller:

````
$this->addFlash('success', 'Testo');
````

In Twig:

````
{% for message in app.flashes('success') %}
    <div class="alert alert-success">
        {{ message }}
    </div>
{% end for %}
````

Per controllare se ci sono flash senza rimuoverli:

````
{{ app.session.flashbag.peek('success')|length }}
````