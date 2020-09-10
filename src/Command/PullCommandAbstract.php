<?php

namespace App\Command;

use App\Entity\Orden;
use App\Entity\Exchange;
use App\Model\Libro;
use App\Util\AbstractClient;
use Symfony\Component\Console\Command\Command;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class PullCommandAbstract extends Command
{
    protected EntityManagerInterface $em;

    protected OutputInterface $output;

    protected function actualizarExchangePares(Exchange $exchange, AbstractClient $cliente, array $pares)
    {
        foreach ($pares as $par) {
            $this->output->writeln('Actualizando desde ' . $exchange->getNombre() . ', par ' . $par);
            $libroActualizado = $cliente->getLibro($par);

            $ordenesEliminadas = $this->actualizarLibroExchange($exchange, $libroActualizado);

            if ($ordenesEliminadas) {
                foreach ($ordenesEliminadas as $ordenEliminada) {
                    $this->em->remove($ordenEliminada);
                }
            }

            $this->output->writeln('  Ordenes total: ' . $libroActualizado->count());
            $this->output->writeln('  Ordenes eliminadas: ' . count($ordenesEliminadas));

            $coti = $exchange->obtenerCotizacionPar($par);
            if ($coti == null) {
                $coti = $exchange->obtenerCotizacionDesdeLibro($par);
            }
            $coti->setFecha(new \DateTime());
        }

        $this->em->persist($exchange);
        $this->em->flush();
    }

    protected function actualizarCotizacion(Exchange $exchange, string $par, \App\Entity\Cotizacion $cotizacion): \App\Entity\Cotizacion
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

    protected function actualizarLibroExchange(Exchange $exchange, Libro $libroActualizado): array
    {
        $par = $libroActualizado->getPar();

        // Marcar todas las órdenes del exchange como inactivas
        foreach ($exchange->obtenerOrdenesPorPar($par) as $ordenLibro) {
            $ordenLibro->setActiva(false);
        }

        $ordenesArray = $exchange->obtenerOrdenesPorPar($par);

        // A continuación, reactivas las que aun existen en el libro actualizado
        foreach ($libroActualizado->getOrdenes() as $ordenlibroactualizado) {
            $ordenesExistenteArray = array_filter($ordenesArray, function (Orden $ordenExchange) use ($ordenlibroactualizado) {
                return $ordenExchange->getActiva() == false
                    && $ordenExchange->getLado() == $ordenlibroactualizado->getLado()
                    && $ordenExchange->getPrecio() == $ordenlibroactualizado->getPrecio()
                    && $ordenExchange->getCantidad() == $ordenlibroactualizado->getCantidad()                    
                    ;
            });

            if (is_array($ordenesExistenteArray) && count($ordenesExistenteArray) > 0) {
                $ordenactualizada = reset($ordenesExistenteArray);
            } else {
                $ordenactualizada = new Orden();
                $ordenactualizada->setFecha(new \Datetime());
                $ordenactualizada->setLado($ordenlibroactualizado->getLado());
                $ordenactualizada->setPar($par);
            }

            $ordenactualizada->setExchange($exchange);
            $ordenactualizada->setPrecio($ordenlibroactualizado->getPrecio());
            $ordenactualizada->setCantidad($ordenlibroactualizado->getCantidad());
            $ordenactualizada->setActiva(true);

            $exchange->addOrden($ordenactualizada);
        }

        $ordenesEliminadas = [];
        foreach ($exchange->obtenerOrdenesPorPar($par) as $ordenLibro) {
            if ($ordenLibro->getActiva() == false) {
                $exchange->removeOrden($ordenLibro);
                $ordenesEliminadas[] = $ordenLibro;
            }
        }

        return $ordenesEliminadas;
    }
}
