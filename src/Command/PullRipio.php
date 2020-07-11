<?php

namespace App\Command;

use App\Entity\Orden;
use App\Entity\Exchange;
use App\Model\Libro;
use App\Util\RipioClient;
use App\Command\PullCommandAbstract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PullRipio extends PullCommandAbstract
{
    protected static $defaultName = 'pull:ripio';

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function configure()
    {
        $this->setDescription('Actualiza datos desde Ripio.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $exchange = $this->em->getRepository('App\Entity\Exchange')->find(9000);
        $cliente = new RipioClient(getenv('API_KEY_BITSO'));
        $pares = ['BTC/ARS', 'ETH/ARS', 'USDC/ARS', 'BTC/USDC', 'ETH/USDC', 'ETH/BTC'];

        $this->actualizarExchangePares($exchange, $cliente, $pares);

        return 0;
    }
}
