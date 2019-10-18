<?php

namespace App\Util;

use App\Model\Order;
use App\Model\OrderBook;
use App\Model\Rate;

class AnalizadorRipio
{
    /** @var OrderBook */
    private $orderBookRipioBtcArs;

    /** @var OrderBook */
    private $orderBookRipioEthArs;

    /** @var OrderBook */
    private $orderBookDolar;

    /** @var Rate */
    private $referenceBtcUsd;

    /** @var Rate */
    private $referenceEthUsd;

    /** @var Rate */
    private $referenceEthBtc;

    public function __construct(OrderBook $ripioBtc, OrderBook $ripioEth, OrderBook $dolar, Rate $referenceBtcUsd, Rate $referenceEthUsd, Rate $referenceEthBtc)
    {
        $this->orderBookRipioBtcArs = $ripioBtc;
        $this->orderBookRipioEthArs = $ripioEth;
        $this->orderBookDolar = $dolar;
        $this->referenceBtcUsd = $referenceBtcUsd;
        $this->referenceEthUsd = $referenceEthUsd;
        $this->referenceEthBtc = $referenceEthBtc;
    }

    public function calcularGapArbitrageBtcEth(): float
    {
        $precioVentaBtc = $this->getRipioBuyBtc();
        $precioCompraEth = $this->getRipioSellEth();

        $ethPorBtcEnRipio = $precioVentaBtc / $precioCompraEth;
        $btcPorEthEnRef = ($ethPorBtcEnRipio * $this->referenceEthBtc->getSellPrice()) - 1;

        return $btcPorEthEnRef;
    }

    public function calcularGapArbitrageEthBtc(): float
    {
        $precioVentaEth = $this->getRipioBuyEth();
        $precioCompraBtc = $this->getRipioSellBtc();

        $btcPorEthEnRipio = $precioVentaEth / $precioCompraBtc;
        $ethPorBtcEnRef = ($btcPorEthEnRipio / $this->referenceEthBtc->getSellPrice()) - 1;

        return $ethPorBtcEnRef;
    }

    public function getGapBtc(): float
    {
        return 1 - ($this->getRipioBuyBtc() / $this->getRipioSellBtc());
    }

    public function getGapEth(): float
    {
        return 1 - ($this->getRipioBuyEth() / $this->getRipioSellEth());
    }

    public function getDolar(): float
    {
        return $this->orderBookDolar->getBestSellPrice();
    }

    public function getReferenceBtcUsd(): Rate
    {
        return $this->referenceBtcUsd;
    }

    public function getReferenceEthUsd(): Rate
    {
        return $this->referenceEthUsd;
    }

    public function getReferenceEthBtc(): Rate
    {
        return $this->referenceEthBtc;
    }

    public function getRipioBuyBtc(): float
    {
        return $this->orderBookRipioBtcArs->getBestBuyPrice(1000);
    }

    public function getRipioBuyEth(): float
    {
        return $this->orderBookRipioEthArs->getBestBuyPrice(1000);
    }

    public function getRipioSellBtc(): float
    {
        return $this->orderBookRipioBtcArs->getBestSellPrice(1000);
    }

    public function getRipioSellEth(): float
    {
        return $this->orderBookRipioEthArs->getBestSellPrice(1000);
    }

    public function getRipioBuyGapBtc(): float
    {
        return $this->orderBookRipioBtcArs->getBestBuyPrice() / ($this->getReferenceBtcUsd()->getSellPrice() * $this->getDolar()) - 1;
    }

    public function getRipioBuyGapEth(): float
    {
        return $this->orderBookRipioEthArs->getBestBuyPrice() / ($this->getReferenceEthUsd()->getSellPrice() * $this->getDolar()) - 1;
    }

    public function getRipioSellGapBtc(): float
    {
        return $this->orderBookRipioBtcArs->getBestSellPrice() / ($this->getReferenceBtcUsd()->getSellPrice() * $this->getDolar()) - 1;
    }

    public function getRipioSellGapEth(): float
    {
        return $this->orderBookRipioEthArs->getBestSellPrice() / ($this->getReferenceEthUsd()->getSellPrice() * $this->getDolar()) - 1;
    }
}
