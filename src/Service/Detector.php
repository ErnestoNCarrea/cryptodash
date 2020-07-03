<?php

namespace App\Service;

use App\Entity\Exchange;
use App\Entity\Orden;
use App\Entity\Pierna;
use App\Entity\Oportunidad;
use App\Model\Libro;
use Psr\Log\LoggerInterface;

/**
 * Analiza un libro de órdenes y detecta oportunidades de arbitraje.
 */
class Detector
{
    private Libro $libro;

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Devuelve una lista de todas las oportunidades de arbitraje detectadas
     * en una lista de órdenes.
     * 
     * @return Oportunidad[]|array|null
     */
    public function detectarOportunidades(): ?array
    {
        $this->logger->info('detectarOportunidades para ' . $this->libro->count());

        $todasLasDivisas = $this->libro->obtenerTodasLasDivisas();

        $this->logger->info('detectarOportunidades para las divisas', $todasLasDivisas);

        $oportunidades = [];

        // Buscar oportunidades para cada par posible
        foreach($todasLasDivisas as $divisaDesde) {
            foreach($todasLasDivisas as $divisaHacia) {
                if ($divisaDesde != $divisaHacia) {
                    $res = $this->detectarOportunidadesPar($divisaDesde . '/' . $divisaHacia);
                    if ($res) {
                        // Agregar las oportunidades de este par al resultado general
                        $oportunidades = array_merge($oportunidades, $res);
                    }
                }
            }
        }

        return $oportunidades;
    }

    /**
     * Devuelve una lista de todas las oportunidades de arbitraje detectadas
     * para un par determinado, o null si no se encuentran oportunidades.
     * 
     * @return Oportunidad[]|array|null
     */
    public function detectarOportunidadesPar(string $par): ?array
    {
        $this->logger->info('detectarOportunidadesPar para {par}', ['par' => $par]);

        $oportunidades = [];
        
        // Buscar en cada lado del libro
        // TODO: el orden de los lados debe decidirse basándose en la preferencia
        // de obtener la ganacias en una moneda o en la otra
        foreach([ Orden::LADO_VENTA, Orden::LADO_COMPRA ] as $lado) {
            $this->logger->info('detectarOportunidadesPar en el libro de {lado}', [ 'lado' => Orden::LADOS_NOMBRES[$lado]]);
            $mejorOferta = $this->libro->obtenerMejorOferta($lado, $par);
            $this->logger->info('Mejor oferta: {orden}', ['orden' => $mejorOferta]);
            if ($mejorOferta) {
                $otraPierna = $this->buscarOtraPierna($par, $mejorOferta);
                if ($otraPierna) {
                    // Crear una oportunidad
                    $opor = new Oportunidad();
                    $opor->addPierna($mejorOferta);
                    do {
                        // Existe una diferencia arbitrable bruta.
                        $this->logger->info('Otra pierna: {orden}', ['orden' => $otraPierna]);
                        $opor->addPierna($otraPierna);

                        // Eliminar el volumen de los libros, para seguir buscando
                        // oportunidades y no volver a encontrar la misma
                        $this->quitarVolumenDelLibro($opor);

                        // Seguir buscando más piernas, hasta agotar el volumen
                    } while (null !== ($otraPierna = $this->buscarOtraPierna($par, $mejorOferta)));
                    $this->restablecerVolumenDelLibro();

                    // Agregar esta oportunidad al resultado
                    $oportunidades[] = $opor;

                    $this->logger->info('Oportunidad: {opor}', ['opor' => $opor]);
                }
            }
        }

        if (count($oportunidades) == 0) {
            return null;
        } else {
            return $oportunidades;
        }
    }

    /**
     * Restablece a su valor inicial todo el volumen en el libro para analizar nuevamente.
     */
    private function restablecerVolumenDelLibro()
    {
        $this->libro->restablecerVolumenDelLibro();
    }

    /**
     * Elimina total o parcialmente una o más ordenes del libro.
     */
    private function quitarVolumenDelLibro(Oportunidad $opor)
    {
        $cantidad = $opor->getCantidadRemanente();

        // Eliminar
        foreach($opor->getPiernas() as $pierna) {
            if ($pierna->getCantidadRemanente() >= $cantidad) {
                // Consumir la orden totalmente (eliminarla del libro)
                $this->libro->elminarOrdenPorId($pierna->getId());
                $pierna->setCantidadRemanente(0);
            } else {
                // Consumir la orden parcialmente. Queda en el libro, pero
                // se resta la cantidad que fue considerada en este arbitraje
                // para que no se tome en cuenta en nuevas búsquedas.

                // Calcular la cantidad remanente
                $cantidadActual = $pierna->getCantidadRemanente();
                $cantidadNueva = $cantidadActual - $cantidad;

                //$orden = $this->libro->obtenerOrdenPorId($pierna->getId());
                $pierna->setCantidadRemanente($cantidadNueva);
            }
        }
    }

    /** 
     * Devuelve una orden arbitrable a partir de una orden, o null si no se encuentra ninguna.
     **/
    private function buscarOtraPierna(string $par, Orden $orden) : ?Orden
    {
        $otroLado = $orden->getLado() == Orden::LADO_VENTA ? Orden::LADO_COMPRA : Orden::LADO_VENTA;
        $otraPierna = $this->libro->obtenerMejorOferta($otroLado, $par);

        if ($this->existeDiferenciaBruta($orden, $otraPierna)) {
            // Existe una diferencia bruta (falta descontar comisiones)
            return $otraPierna;
        } else {
            // No hay dierencia a favor
            return null;
        }
    }

    /** 
     * Devuelve verdadero si existe una diferencia arbitrable bruta (sin contar
     * comisiones) entre ambas órdenes.
     **/
    private function existeDiferenciaBruta($orden1, $orden2) : bool
    {
        if ($orden1->getLado() == $orden2->getLado()) {
            throw new Exception('No de pueden comparar dos piernas del mismo lado.');
        }

        if ($orden1->getLado() == Orden::LADO_VENTA) {
            // El que vende lo hace a precio más bajo que el que compra?
            return $orden1->getPrecio() < $orden2->getPrecio();
        } else {
            // El que compra lo hace a precio más alto que el que vende?
            return $orden1->getPrecio() > $orden2->getPrecio();
        }
    }

    /**
     * @ignore
     */ 
    public function setLibro(Libro $libro): self
    {
        $this->libro = $libro;

        return $this;
    }
}