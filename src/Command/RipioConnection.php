<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use App\Util\RipioClient;
use App\Model\OrderBook;
use App\Entity\BookOrder;
use App\Entity\Exchange;

class RipioConnection extends Command
{
    //** @var EntityManagerInterface */
    private $em;

    protected static $defaultName = 'connection:ripio';

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function configure()
    {
        $this->setDescription('Establece una conexión con Ripio.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ripioExchange = $this->em->getRepository('App\Entity\Exchange')->find(9000);
        $clientRipio = new RipioClient('8f2104688f50a866fe648be370c9d80ef04d2203c59a1dc5ee8eea7118a94e6f');

        foreach (['BTC/ARS', 'BTC/ARS'] as $pair) {
            //$ripioOrderBook = $this->em->getRepository('App\Entity\BookOrder')->findBy(['exchange' => $ripioExchange, 'pair' => $pair, 'user' => null]);
            $orderBook = $clientRipio->getOrderBook($pair);

            $this->updateOrderBook($ripioExchange, $pair, $orderBook);
        }

        $this->em->persist($ripioExchange);
    }

    private function updateOrderBook(Exchange $exchange, string $pair, OrderBook $updatedOrderBook)
    {
        foreach ($updatedOrderBook->getBuyOrders() as $order) {
            $orderEntityArray = $exchange->getBookOrders()->filter(function (BookOrder $orderEntity) use ($order) {
                return $order->getPrice() == $orderEntity->getPrice();
            });

            if (is_array($orderEntityArray) && count($orderEntityArray) == 1) {
                $orderEntity = $orderEntityArray[0];
            } else {
                $orderEntity = new BookOrder();
            }

            $orderEntity->setSide(BookOrder::SIDE_BUY);
            $orderEntity->setExchange($exchange);
            $orderEntity->setPrice($order->getPrice());
            $orderEntity->setQuantity($order->getQuantity());
            $orderEntity->setPair($pair);

            echo "create\n";
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
            }

            $orderEntity->setSide(BookOrder::SIDE_SELL);
            $orderEntity->setExchange($exchange);
            $orderEntity->setPrice($order->getPrice());
            $orderEntity->setQuantity($order->getQuantity());
            $orderEntity->setPair($pair);

            $exchange->addBookOrder($orderEntity);
        }
    }
}
