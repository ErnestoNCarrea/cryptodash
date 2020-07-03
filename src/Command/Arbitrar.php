<?php

namespace App\Command;

use App\Entity\Orden;
use App\Entity\Exchange;
use App\Model\Libro;
use App\Service\DetectorDeOportunidades;
use App\Service\Arbitrador;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Arbitrar extends Command
{
    protected static $defaultName = 'arbitrar';

    private EntityManagerInterface $em;

    private DetectorDeOportunidades $detector;
    private Arbitrador $arbitrador;

    public function __construct(EntityManagerInterface $em, DetectorDeOportunidades $detector, Arbitrador $arbitrador)
    {
        parent::__construct();

        $this->em = $em;
        $this->detector = $detector;
        $this->arbitrador = $arbitrador;
    }

    protected function configure()
    {
        $this->setDescription('Proceso principal de arbitraje.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Buscando oportunidades de arbitraje');

        $ordenes = $this->em->getRepository('App\Entity\Orden')->findAll(); //By(['activo' => 1]);
        $this->detector->setLibro(new Libro($ordenes));

        $oportunidades = $this->detector->detectarOportunidadesPar('BTC/ARS');

        if ($oportunidades == null) {
            $output->writeln('No se encontraron oportunidades.');
        } else {
            $output->writeln('Se encontraron ' . count($oportunidades) . ' oportunidades.');
        }

        
        if ($oportunidades) {
            # Persistir oportunidades detectadas
            /* foreach ($oportunidades as $oportunidad) {
                $this->em->persist($oportunidad);
            }
            $this->em->flush(); */

            # Ejecutar arbitrajes
            $this->arbitrador->arbitrarOprtunidades($oportunidades);

            return 1;
        } else {
            return 0;
        }
    }
}
