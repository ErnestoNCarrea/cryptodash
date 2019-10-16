<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RipioConnection extends Command
{
    protected static $defaultName = 'connection:ripio';

    protected function configure()
    {
        $this
            ->setDescription('Establece una conexión con Ripio.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        \Ratchet\Client\connect('ws://api.exchange.ripio.com:8443/ws/v2/consumer/non-persistent/public/default/orderbook_eth_ars/ZXF1aXN0YW5nb0BnbWFpbC5jb20=')->then(function ($conn) {
            $conn->on('message', function ($msg) use ($conn) {
                echo "Received: {$msg}\n";
                $conn->close();
            });

            $conn->send('{
                "event": "subscribe",
                    "data": {
                        "channel": "OrderBook",
                        "symbol": "BTCARS"
                    }
                }
            ');
        }, function ($e) {
            echo "Could not connect: {$e->getMessage()}\n";
        });
    }
}
