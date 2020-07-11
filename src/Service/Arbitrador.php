<?php

namespace App\Service;

use App\Entity\Exchange;
use App\Entity\Orden;
use App\Entity\Pierna;
use App\Entity\Oportunidad;
use App\Model\Libro;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Servicio principal del ejecución de arbitrajes.
 */
class Arbitrador
{
    private EntityManagerInterface $em;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * Persiste las oportunidades.
     */
    public function persistirOportunidades(?array $oportunidades)
    {
        $oportunidadesExistentes = $this->em->getRepository('App\Entity\Oportunidad')->findAll();

        // Desactivar todas las oportunidades. Luego se reactivarán las que
        // aun se encuentran vigentes.
        foreach($oportunidadesExistentes as $opor) {
            $opor->setActiva(false);
        }

        foreach($oportunidades as $opor) {
            $oporExistente = $this->buscarOportunidadIgualEnColeccion($opor, $oportunidadesExistentes);
            if ($oporExistente === null) {
                // No existe la oportunidad. Agregarla.
                $oportunidadesExistentes[] = $opor;
            } else {
                // Ya existe una oportunidad igual. Reactivar.
                $oporExistente->setActiva(true);
                $oporExistente->setContador($oporExistente->getContador() + 1);
                $oporExistente->setFecha(new \Datetime());
            }
        }

        // TODO: eliminar oportunidades antiguas e inadtivas

        // Persistir todas las oportunidades
        foreach($oportunidadesExistentes as $opor) {
            $this->em->persist($opor);
        }

        $this->em->flush();
    }

    /**
     * Devuelve true si en un conjunto existe una oportunidad igual a la proporcionada.
     */
    private function buscarOportunidadIgualEnColeccion(Oportunidad $opor, $oportunidades) : ?Oportunidad
    {
        foreach($oportunidades as $oporex) {
            if (Oportunidad::areEqual($opor, $oporex)) {
                return $oporex;
            }
        }
        return null;
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
