<?php

namespace App\Util;

use App\Model\Order;
use App\Model\OrderBook;
use App\Model\AnalisisRipio;

class AnalizadorRipio
{
    /** @var OrderBook */
    private $orderBookRipioBtcArs;

    /** @var OrderBook */
    private $orderBookRipioEthArs;

    /** @var OrderBook */
    private $orderBookDolar;

    /** @var OrderBook */
    private $orderBookReferenceBtcUsd;

    /** @var OrderBook */
    private $orderBookReferenceEthUsd;

    /** @var OrderBook */
    private $orderBookReferenceEthBtc;

    public function __construct(OrderBook $ripioBtc, OrderBook $ripioEth, OrderBook $dolar, OrderBook $referenceBtcUsd, OrderBook $referenceEthUsd, OrderBook $referenceEthBtc)
    {
        $this->orderBookRipioBtcArs = $ripioBtc;
        $this->orderBookRipioEthArs = $ripioEth;
        $this->orderBookDolar = $dolar;
        $this->orderBookReferenceBtcUsd = $referenceBtcUsd;
        $this->orderBookReferenceEthUsd = $referenceEthUsd;
        $this->orderBookReferenceEthBtc = $referenceEthBtc;
    }

    public function calcularGapArbitrageBtcEth() : float {
        $precioVentaBtc = $this->getRipioBuyBtc();
        $precioCompraEth = $this->getRipioSellEth();

        $ethPorBtcEnRipio = $precioVentaBtc / $precioCompraEth;
        $btcPorEthEnRef = ($ethPorBtcEnRipio * $this->orderBookReferenceEthBtc->getBestBuyPrice()) - 1;

        return $btcPorEthEnRef;
    }

    public function calcularGapArbitrageEthBtc() : float {
        $precioVentaEth = $this->getRipioBuyEth();
        $precioCompraBtc = $this->getRipioSellBtc();

        $btcPorEthEnRipio = $precioVentaEth / $precioCompraBtc;
        $ethPorBtcEnRef = ($btcPorEthEnRipio / $this->orderBookReferenceEthBtc->getBestSellPrice()) - 1;

        return $ethPorBtcEnRef;
    }

    public function getDolar() : float {
        return $this->orderBookDolar->getBestBuyPrice();
    }

    public function getReferenceBtcUsd() : float {
        return $this->orderBookReferenceBtcUsd->getBestBuyPrice();
    }

    public function getReferenceEthUsd() : float {
        return $this->orderBookReferenceEthUsd->getBestBuyPrice();
    }

    public function getReferenceEthBtc() : float {
        return $this->orderBookReferenceEthBtc->getBestBuyPrice();
    }
    
    public function getRipioBuyBtc() : float {
        return $this->orderBookRipioBtcArs->getBestBuyPrice(1000);
    }

    public function getRipioBuyEth() : float {
        return $this->orderBookRipioEthArs->getBestBuyPrice(1000);
    }

    public function getRipioSellBtc() : float {
        return $this->orderBookRipioBtcArs->getBestSellPrice(1000);
    }

    public function getRipioSellEth() : float {
        return $this->orderBookRipioEthArs->getBestSellPrice(1000);
    }

    public function getRipioBuyGapBtc() : float {
        return $this->orderBookRipioBtcArs->getBestBuyPrice() / ($this->orderBookReferenceBtcUsd->getBestBuyPrice() * $this->getDolar()) - 1;
    }

    public function getRipioBuyGapEth() : float {
        return $this->orderBookRipioEthArs->getBestBuyPrice() / ($this->orderBookReferenceEthUsd->getBestBuyPrice() * $this->getDolar()) - 1;
    }

    public function getRipioSellGapBtc() : float {
        return $this->orderBookRipioBtcArs->getBestSellPrice() / ($this->orderBookReferenceBtcUsd->getBestSellPrice() * $this->getDolar()) - 1;
    }

    public function getRipioSellGapEth() : float {
        return $this->orderBookRipioEthArs->getBestSellPrice() / ($this->orderBookReferenceEthUsd->getBestSellPrice() * $this->getDolar()) - 1;
    }
}