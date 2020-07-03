<?php

namespace App\Command;

use App\Entity\Orden;
use App\Entity\Exchange;
use App\Model\Libro;
use App\Util\BinanceClient;
use App\Command\PullCommandAbstract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PullBinance extends PullCommandAbstract
{
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
        $this->output = $output;

        $exchange = $this->em->getRepository('App\Entity\Exchange')->find(1000);
        $cliente = new BinanceClient();
        $pares = ['BTC/USD', 'ETH/USD', 'ETH/BTC', 'XLM/USD', 'EOS/USD'];

        //$this->actualizarExchangePares($exchange, $cliente, $pares);

        foreach ($pares as $par) {
            $cotizacion = $this->actualizarCotizacion($exchange, $par, $cliente->getPrecioActual($par));
            if ($cotizacion) {
                $this->em->persist($cotizacion);
            }
        }

        $this->em->persist($exchange);
        $this->em->flush();

        return 0;
    }
}
