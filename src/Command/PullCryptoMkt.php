<?php

namespace App\Command;

use App\Entity\Orden;
use App\Entity\Exchange;
use App\Model\Libro;
use App\Util\CryptoMktClient;
use App\Command\PullCommandAbstract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PullCryptoMkt extends PullCommandAbstract
{
    protected static $defaultName = 'pull:cryptomkt';

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function configure()
    {
        $this->setDescription('Actualiza datos desde CryptoMkt.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $exchange = $this->em->getRepository('App\Entity\Exchange')->find(9003);
        $cliente = new CryptoMktClient();
        $pares = ['BTC/ARS', 'ETH/ARS', 'XLM/ARS', 'EOS/ARS'];

        $this->actualizarExchangePares($exchange, $cliente, $pares);

        return 0;
    }
}
