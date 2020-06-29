<?php

namespace App\Command;

use App\Entity\Orden;
use App\Entity\Exchange;
use App\Model\Libro;
use App\Util\BuenbitClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PullBuenbit extends Command
{
    //** @var EntityManagerInterface */
    private $em;

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
        $output->writeln('Actualizando desde Buenbit');

        $exchange = $this->em->getRepository('App\Entity\Exchange')->find(9002);
        $clientRipio = new BuenbitClient();

        foreach (['BTC/ARS', 'ETH/ARS'] as $pair) {
            $libro = $clientRipio->getLibro($pair);

            $ordenesEliminadas = $this->updateLibro($exchange, $pair, $libro);
            if ($ordenesEliminadas) {
                foreach ($ordenesEliminadas as $ordenEliminada) {
                    $this->em->remove($ordenEliminada);
                }
            }

            $rate = $this->updateRate($exchange, $pair, $clientRipio->getCurrentPrice($pair));
            if ($rate) {
                $this->em->persist($rate);
            }
        }

        $this->em->persist($exchange);
        $this->em->flush();

        return 0;
    }

    private function updateRate(Exchange $exchange, string $pair, \App\Model\Rate $rate): \App\Entity\Rate
    {
        $rateEntity = $exchange->getCurrentRateForPair($pair);
        if ($rateEntity === null) {
            $rateEntity = new \App\Entity\Rate();
            $exchange->addCurrentRate($rateEntity);
        }

        $rateEntity->setPair($pair);
        $rateEntity->setBuyPrice($rate->getBuyPrice());
        $rateEntity->setSellPrice($rate->getSellPrice());
        $rateEntity->setDateTime(new \Datetime());

        return $rateEntity;
    }

    private function updateLibro(Exchange $exchange, string $pair, Libro $updatedLibro): array
    {
        foreach ($exchange->getOrdens() as $ordenLibro) {
            if ($ordenLibro->getPair() == $pair) {
                $ordenLibro->setActive(false);
            }
        }

        foreach ($updatedLibro->getOrdenesCompra() as $order) {
            $orderEntityArray = $exchange->getOrdens()->filter(function (Orden $orderEntity) use ($order) {
                return $order->getPrice() == $orderEntity->getPrice();
            });

            if (is_array($orderEntityArray) && count($orderEntityArray) == 1) {
                $orderEntity = $orderEntityArray[0];
            } else {
                $orderEntity = new Orden();
                $orderEntity->setDateTime(new \Datetime());
            }

            $orderEntity->setSide(Orden::SIDE_BUY);
            $orderEntity->setExchange($exchange);
            $orderEntity->setPrice($order->getPrice());
            $orderEntity->setQuantity($order->getQuantity());
            $orderEntity->setPair($pair);
            $orderEntity->setActive(true);

            $exchange->addOrden($orderEntity);
        }

        foreach ($updatedLibro->getOrdenesVenta() as $order) {
            $orderEntityArray = $exchange->getOrdens()->filter(function (Orden $orderEntity) use ($order) {
                return $order->getPrice() == $orderEntity->getPrice();
            });

            if (is_array($orderEntityArray) && count($orderEntityArray) == 1) {
                $orderEntity = $orderEntityArray[0];
            } else {
                $orderEntity = new Orden();
                $orderEntity->setDateTime(new \Datetime());
            }

            $orderEntity->setSide(Orden::SIDE_SELL);
            $orderEntity->setExchange($exchange);
            $orderEntity->setPrice($order->getPrice());
            $orderEntity->setQuantity($order->getQuantity());
            $orderEntity->setPair($pair);
            $orderEntity->setActive(true);

            $exchange->addOrden($orderEntity);
        }

        $res = [];
        foreach ($exchange->getOrdens() as $ordenLibro) {
            if ($ordenLibro->getPair() == $pair && $ordenLibro->getActive() == false) {
                $exchange->removeOrden($ordenLibro);
                $res[] = $ordenLibro;
            }
        }

        return $res;
    }
}
