<?php

namespace App\Command;

use App\Entity\Orden;
use App\Entity\Exchange;
use App\Model\Libro;
use App\Util\BinanceClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PullBinance extends Command
{
    //** @var EntityManagerInterface */
    private $em;

    protected static $defaultName = 'pull:binance';

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function configure()
    {
        $this->setDescription('Actualiza datos desde Binance.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Actualizando desde Binance');

        $exchange = $this->em->getRepository('App\Entity\Exchange')->find(1000);
        $exchangeClient = new BinanceClient();

        foreach (['BTC/USD', 'ETH/USD', 'ETH/BTC', 'XLM/USD', 'EOS/USD'] as $par) {
            /* $libro = $exchangeClient->getLibro($par);

            $ordenesEliminadas = $this->updateLibro($exchange, $par, $libro);
            if ($ordenesEliminadas) {
            foreach ($ordenesEliminadas as $ordenEliminada) {
            $this->em->remove($ordenEliminada);
            }
            } */

            $cotizacion = $this->updateCotizacion($exchange, $par, $exchangeClient->getPrecioActual($par));
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
