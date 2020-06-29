<?php

namespace App\Command;

use App\Entity\BookOrder;
use App\Entity\Exchange;
use App\Model\OrderBook;
use App\Util\BitsoClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PullBitso extends Command
{
    //** @var EntityManagerInterface */
    private $em;

    protected static $defaultName = 'pull:bitso';

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function configure()
    {
        $this->setDescription('Actualiza datos desde Bitso.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Actualizando desde Bitso');

        $exchange = $this->em->getRepository('App\Entity\Exchange')->find(9004);
        $clientBitso = new BitsoClient('8f2104688f50a866fe648be370c9d80ef04d2203c59a1dc5ee8eea7118a94e6f');

        foreach (['BTC/ARS', 'ETH/BTC', 'XRP/BTC'] as $pair) {
            $orderBook = $clientBitso->getOrderBook($pair);

            $deletedOrders = $this->updateOrderBook($exchange, $pair, $orderBook);
            if ($deletedOrders) {
                foreach ($deletedOrders as $deletedOrder) {
                    $this->em->remove($deletedOrder);
                }
            }

            $rate = $this->updateRate($exchange, $pair, $clientBitso->getCurrentPrice($pair));
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

    private function updateOrderBook(Exchange $exchange, string $pair, OrderBook $updatedOrderBook): array
    {
        foreach ($exchange->getBookOrders() as $bookOrder) {
            if ($bookOrder->getPair() == $pair) {
                $bookOrder->setActive(false);
            }
        }

        foreach ($updatedOrderBook->getBuyOrders() as $order) {
            $orderEntityArray = $exchange->getBookOrders()->filter(function (BookOrder $orderEntity) use ($order) {
                return $order->getPrice() == $orderEntity->getPrice();
            });

            if (is_array($orderEntityArray) && count($orderEntityArray) == 1) {
                $orderEntity = $orderEntityArray[0];
            } else {
                $orderEntity = new BookOrder();
                $orderEntity->setDateTime(new \Datetime());
            }

            $orderEntity->setSide(BookOrder::SIDE_BUY);
            $orderEntity->setExchange($exchange);
            $orderEntity->setPrice($order->getPrice());
            $orderEntity->setQuantity($order->getQuantity());
            $orderEntity->setPair($pair);
            $orderEntity->setActive(true);

            $exchange->addBookOrder($orderEntity);
        }

        foreach ($updatedOrderBook->getSellOrders() as $order) {
            $orderEntityArray = $exchange->getBookOrders()->filter(function (BookOrder $orderEntity) use ($order) {
                return $order->getPrice() == $orderEntity->getPrice();
            });

            if (is_array($orderEntityArray) && count($orderEntityArray) == 1) {
                $orderEntity = $orderEntityArray[0];
            } else {
                $orderEntity = new BookOrder();
                $orderEntity->setDateTime(new \Datetime());
            }

            $orderEntity->setSide(BookOrder::SIDE_SELL);
            $orderEntity->setExchange($exchange);
            $orderEntity->setPrice($order->getPrice());
            $orderEntity->setQuantity($order->getQuantity());
            $orderEntity->setPair($pair);
            $orderEntity->setActive(true);

            $exchange->addBookOrder($orderEntity);
        }

        $res = [];
        foreach ($exchange->getBookOrders() as $bookOrder) {
            if ($bookOrder->getPair() == $pair && $bookOrder->getActive() == false) {
                $exchange->removeBookOrder($bookOrder);
                $res[] = $bookOrder;
            }
        }

        return $res;
    }
}
