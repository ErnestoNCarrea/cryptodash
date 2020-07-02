<?php

namespace App\Service;

use App\Entity\Exchange;
use App\Entity\Orden;
use App\Entity\Pierna;
use App\Entity\Oportunidad;
use App\Model\Libro;
use Psr\Log\LoggerInterface;

/**
 * Servicio principal del ejecución de arbitrajes.
 */
class Arbitrador
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Ejecuta las operaciones de arbitraje para aprovechar las oportunidades detectadas.
     */
    public function arbitrarOprtunidades(array $oportunidades)
    {
        foreach ($oportunidades as $opor) {
            $this->arbitrarOprtunidad($opor);
        }
    }

    /**
     * Ejecuta un arbitraje en base a una oportunidad detectada.
     */
    public function arbitrarOprtunidad(Oportunidad $opor)
    {
        $this->logger->notice('arbitrarOprtunidad: {opor}', ['opor' => $opor]);

        echo "*** EJECUTAR ARBITRAJE:\n";
        echo (string)$opor;
    }
  
}
