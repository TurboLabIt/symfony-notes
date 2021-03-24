# Form con Symfony

## Installazione

`symfony composer require form`

## Generazione di un form

`symfony console make:form Reports`

Questo genera un *FormType* nella cartella `Form`.

## Aggiunta campi

Nel file `Form/ReportsType.php`, personalizzare `buildForm()`:

````
$builder
    ->add('start_date')
    ->add('end_date')
    ->add('partner')
;
````

## Preparazione del form nel controller

````
public function index(Request $request, BuyOrderRepository $repoOrder, Excel $excel): Response
{
    $form =
        $this->createForm(ReportType::class)
            ->handleRequest($request);

    if( $form->isSubmitted() && $form->isValid() ) {

        $formData   = $form->getData();
        $arrOrders  = $repoOrder->getByDate($formData["start_date"], $formData["end_date"], $formData["partner"]);

        if( empty($arrOrders) ) {

            $this->addFlash("No orders", "warning");

        } else {

            $filename = 'report.xlsx';

            $excel
                ->buildOrder($arrOrders)
                ->outToBrowser($filename);

            return new Response('');
        }
    }

    return $this->render('admin/reports.html.twig', [

        'reportForm'    => $form->createView()
    ]);
}
````


## Visualizzazione del form in Twig

````
{% extends '@EasyAdmin/page/content.html.twig' %}

{% block page_content %}
    
    <p>
        {{ form_start(reportForm) }}

            {{ form_widget(reportForm) }}

            <button type="submit" class="btn btn-primary">Download</button>

        {{ form_end(reportForm) }}
    </p>

{% endblock %}
````


## Binding del form su una entity

In FormType:

````
public function configureOptions(OptionsResolver $resolver)
{
    $resolver->setDefaults([
        "data_class" => Report::class
    ]);
}
````

A questo punto, `$form->getData()` ritorna direttamente l'entity con i dati del form.

