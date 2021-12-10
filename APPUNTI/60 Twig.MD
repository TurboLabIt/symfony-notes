# Twig

I template vanno in `templates` (configurabile in `config/packages/twig.yaml`).

Per renderizzarli:

````php
class HomeController extends BaseController
{
    #[Route('/home/{pageNum}', name: 'app_home_num')]
    public function appHomeNum(int $pageNum): Response
    {
        ...
    
        return $this->render('home/index.html.twig', [
    
            "ArticleLatestList"     => ...,
            "ArticleMainList"       => ...
        ]);
    }
}
````

````twig
{% extends 'main.html.twig' %}

...

{% for key,Article in ArticleLatestList %}
    ....
{% endfor %}
````

## Funzioni

1. `{% extends 'principale.html.twig' %}`: estende un template parent (v. seguito)
2. `{% if bao == '1' %}mostra{% endif %}`: costrutti
2. `{% block mao %}mostra{% endbloc %}`: definisce/overwrite blocchi (v. seguito)
1. `{{ bao }}`: stampa una variabile passata dal controller
3. `{# Commento #}`


## Estensione template 

La logica è rovesciata rispetto a Smarty. E' il CHILD che dice dove innestarsi nel PARENT.

Il parent definisce dei BLOCCHI:

````
## principale.html.twig

<p><a href="/">Torna alla home </a></p>
{% block main %}MOSTRATO NEL PRINCIPALE{% endblock %}
{% block additional %}MOSTRATO OVUNQUE{% endblock %}
````

Per sostituire il contenuto presente in un parent da un altro template child:

````
## secondario.html.twig

{% extends 'principale.html.twig' %}
{% block main %}MOSTRATO NEL SECONDARIO{% endblock %}
````

Per appendere, senza pattumare quello che già c'è nel parent:

````
## secondario.html.twig

{% extends 'principale.html.twig' %}
{% block main %}MOSTRATO NEL SECONDARIO{% endblock %}
{% block additional %}
    {{ parent() }}
    MOSTRATO NEL SECONDARIO
{% endblock %}
````

## Inclusione di template

Occhio: è sempre meglio usare l'estensione trattata sopra. Quando non è possibile:

````
{% include 'header.html' %}
    Body
{% include 'footer.html' %}
````

Quando il template che viene incluso si aspetta una variabile dal controller che non è disponibile nello scope corrente:

````
{% include 'template.html' with {'nomeVarAttesaDallIncluso': nomeVarDisponibile} %}
````


## Sostituire blocchi nei template inclusi

Quando si `include` qualcosa non si possono modificare i suoi blocchi.

Per farlo, usiamo `embed` (*It allows you to include another template’s contents, just like include does. But it also allows you to override any block defined inside the included template, like when extending a template.*):

````
{% embed 'booking/progress-bar.html.twig' %}
    {% block step1 %}stepper-element-selected {% endblock %}
    {% block step2 %}stepper-element-selected {% endblock %}
{% endembed %}
````


## Generazione di URL

````twig
{{ path('app_home', { page: 1, var: val }
````