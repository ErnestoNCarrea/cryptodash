<?php

namespace App\Controller;

use App\Entity\Exchange;
use App\Util\AnalizadorRipio;
use App\Util\AnalizadorArbitrajes;
use App\Util\AnalizadorCotizaciones;
use App\Util\DolarIolClient;
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
        $exchangesIds = [ 1000, 9000, 9004, 9003, 9002 ];
        $exchanges = [];
        foreach($exchangesIds as $exchangeId) {
            $exchanges[] = $this->em->getRepository('App\Entity\Exchange')->find($exchangeId);
        }

        $clientDolar = new DolarIolClient();

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
