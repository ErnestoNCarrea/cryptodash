<?php

namespace App\Controller;

use App\Entity\Exchange;
use App\Util\AnalizadorRipio;
use App\Util\AnalizadorArbitrajes;
use App\Util\AnalizadorCotizaciones;
use App\Util\UsdClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/dash")
 */
class DashController extends AbstractController
{
    //** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/coti", name="dash_coti")
     */
    public function coti()
    {
        $exchanges = $this->em->getRepository('App\Entity\Exchange')->findAll();

        $clientDolar = new UsdClient();

        $analizador = new AnalizadorCotizaciones($exchanges);
        $reference = $exchanges[0];

        return $this->render('dash/coti.html.twig', [
            'exchanges' => $exchanges,
            'analizador' => $analizador,
            'reference' => $reference,
            'dolar' => $clientDolar
        ]);
    }

    /**
     * @Route("/libro", name="dash_libro")
     */
    public function libro(Request $request)
    {
        $exchangeId = $request->query->get('exchange');
        $par = $request->query->get('par');

        $exchange = $this->em->getRepository('App\Entity\Exchange')->find($exchangeId);
        $libro = $exchange->obtenerLibroPorPar($par);

        return $this->render('dash/libro.html.twig', [
            'exchange' => $exchange,
            'libro' => $libro,
            'par' => $par,
        ]);
    }
}
