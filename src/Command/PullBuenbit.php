<?php

namespace App\Command;

use App\Entity\Orden;
use App\Entity\Exchange;
use App\Model\Libro;
use App\Util\BuenbitClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PullBuenbit extends Command
{
    //** @var EntityManagerInterface */
    private $em;

    protected static $defaultName = 'pull:buenbit';

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function configure()
    {
        $this->setDescription('Actualiza datos desde Buenbit.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Actualizando desde Buenbit');

        $exchange = $this->em->getRepository('App\Entity\Exchange')->find(9002);
        $clientRipio = new BuenbitClient();

        foreach (['BTC/ARS', 'ETH/ARS'] as $par) {
            $libro = $clientRipio->getLibro($par);

            $ordenesEliminadas = $this->updateLibro($exchange, $par, $libro);
            if ($ordenesEliminadas) {
                foreach ($ordenesEliminadas as $ordenEliminada) {
                    $this->em->remove($ordenEliminada);
                }
            }

            $cotizacion = $this->updateCotizacion($exchange, $par, $clientRipio->getCurrentPrecio($par));
            if ($cotizacion) {
                $this->em->persist($cotizacion);
            }
        }

        $this->em->persist($exchange);
        $this->em->flush();

        return 0;
    }

    private function updateCotizacion(Exchange $exchange, string $par, \App\Entity\Cotizacion $cotizacion): \App\Entity\Cotizacion
    {
        $cotizacionEntity = $exchange->getCotizacionForPar($par);
        if ($cotizacionEntity === null) {
            $cotizacionEntity = new \App\Entity\Cotizacion();
            $exchange->addCotizacion($cotizacionEntity);
        }

        $cotizacionEntity->setPar($par);
        $cotizacionEntity->setPrecioCompra($cotizacion->getPrecioCompra());
        $cotizacionEntity->setPrecioVenta($cotizacion->getPrecioVenta());
        $cotizacionEntity->setDateTime(new \Datetime());

        return $cotizacionEntity;
    }

    private function updateLibro(Exchange $exchange, string $par, Libro $updatedLibro): array
    {
        foreach ($exchange->getOrdenes() as $ordenLibro) {
            if ($ordenLibro->getPar() == $par) {
                $ordenLibro->setActivo(false);
            }
        }

        foreach ($updatedLibro->getOrdenesCompra() as $order) {
            $ordenArray = $exchange->getOrdenes()->filter(function (Orden $orden) use ($order) {
                return $order->getPrecio() == $orden->getPrecio();
            });

            if (is_array($ordenArray) && count($ordenArray) == 1) {
                $orden = $ordenArray[0];
            } else {
                $orden = new Orden();
                $orden->setDateTime(new \Datetime());
            }

            $orden->setLado(Orden::LADO_BUY);
            $orden->setExchange($exchange);
            $orden->setPrecio($order->getPrecio());
            $orden->setCantidad($order->getCantidad());
            $orden->setPar($par);
            $orden->setActivo(true);

            $exchange->addOrden($orden);
        }

        foreach ($updatedLibro->getOrdenesVenta() as $order) {
            $ordenArray = $exchange->getOrdenes()->filter(function (Orden $orden) use ($order) {
                return $order->getPrecio() == $orden->getPrecio();
            });

            if (is_array($ordenArray) && count($ordenArray) == 1) {
                $orden = $ordenArray[0];
            } else {
                $orden = new Orden();
                $orden->setDateTime(new \Datetime());
            }

            $orden->setLado(Orden::LADO_SELL);
            $orden->setExchange($exchange);
            $orden->setPrecio($order->getPrecio());
            $orden->setCantidad($order->getCantidad());
            $orden->setPar($par);
            $orden->setActivo(true);

            $exchange->addOrden($orden);
        }

        $res = [];
        foreach ($exchange->getOrdenes() as $ordenLibro) {
            if ($ordenLibro->getPar() == $par && $ordenLibro->getActivo() == false) {
                $exchange->removeOrden($ordenLibro);
                $res[] = $ordenLibro;
            }
        }

        return $res;
    }
}
