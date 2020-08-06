<?php
namespace App\Controller;

use App\Service\EnvirVars;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class EnvirVarController extends AbstractController
{
    /**
     * @Route("/envir-var/", name="app_envir-var")
     */
    public function index(EnvirVars $envirVars)
    {
        return $this->render('envir_var/index.html.twig', [
            "Pippo"     => $envirVars
        ]);
    }
}
