<?php

namespace App\Command;

use App\Entity\Orden;
use App\Entity\Exchange;
use App\Model\Libro;
use App\Util\UsdClient;
use App\Command\PullCommandAbstract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PullUsd extends PullCommandAbstract
{
    protected static $defaultName = 'pull:usd';

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function configure()
    {
        $this->setDescription('Actualiza datos desde USD Stub Exchange.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $exchange = $this->em->getRepository('App\Entity\Exchange')->find(10);
        $cliente = new UsdClient();
        $pares = ['USDT/USD', 'USDC/USD', 'USDT/USDC'];

        $this->actualizarExchangePares($exchange, $cliente, $pares);

        return 0;
    }
}
