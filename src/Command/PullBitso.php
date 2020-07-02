<?php

namespace App\Command;

use App\Entity\Orden;
use App\Entity\Exchange;
use App\Model\Libro;
use App\Util\BitsoClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PullBitso extends Command
{
    //** @var EntityManagerInterface */
    private $em;

    protected static $defaultName = 'pull:bitso';

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function configure()
    {
        $this->setDescription('Actualiza datos desde Bitso.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Actualizando desde Bitso');

        $exchange = $this->em->getRepository('App\Entity\Exchange')->find(9004);
        $clientBitso = new BitsoClient('8f2104688f50a866fe648be370c9d80ef04d2203c59a1dc5ee8eea7118a94e6f');

        foreach (['BTC/ARS', 'ETH/BTC', 'XRP/BTC'] as $par) {
            $libro = $clientBitso->getLibro($par);

            $ordenesEliminadas = $this->updateLibro($exchange, $par, $libro);
            if ($ordenesEliminadas) {
                foreach ($ordenesEliminadas as $ordenEliminada) {
                    $this->em->remove($ordenEliminada);
                }
            }

            $cotizacion = $this->updateCotizacion($exchange, $par, $clientBitso->getPrecioActual($par));
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
        $cotizacionEntity = $exchange->getCotizacionPar($par);
        if ($cotizacionEntity === null) {
            $cotizacionEntity = new \App\Entity\Cotizacion();
            $exchange->addCotizacion($cotizacionEntity);
        }

        $cotizacionEntity->setPar($par);
        $cotizacionEntity->setPrecioCompra($cotizacion->getPrecioCompra());
        $cotizacionEntity->setPrecioVenta($cotizacion->getPrecioVenta());
        $cotizacionEntity->setFecha(new \Datetime());

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
                $orden->setFecha(new \Datetime());
            }

            $orden->setLado(Orden::LADO_COMPRA);
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
                $orden->setFecha(new \Datetime());
            }

            $orden->setLado(Orden::LADO_VENTA);
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
