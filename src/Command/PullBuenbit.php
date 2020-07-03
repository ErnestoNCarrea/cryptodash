<?php

namespace App\Command;

use App\Entity\Orden;
use App\Entity\Exchange;
use App\Model\Libro;
use App\Util\BuenbitClient;
use App\Command\PullCommandAbstract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PullBuenbit extends PullCommandAbstract
{
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
        $this->output = $output;

        $exchange = $this->em->getRepository('App\Entity\Exchange')->find(9002);
        $cliente = new BuenbitClient();
        $pares = ['BTC/ARS', 'ETH/ARS'];

        $this->actualizarExchangePares($exchange, $cliente, $pares);

        return 0;
    }
}
