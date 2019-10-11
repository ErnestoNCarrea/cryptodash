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

    /** @var float */
    private $referenceBtcUsd;

    /** @var float */
    private $referenceEthUsd;

    /** @var float */
    private $referenceEthBtc;

    public function __construct(OrderBook $ripioBtc, OrderBook $ripioEth, OrderBook $dolar, float $referenceBtcUsd, float $referenceEthUsd, float $referenceEthBtc)
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
        $btcPorEthEnRef = ($ethPorBtcEnRipio * $this->referenceEthBtc) - 1;

        return $btcPorEthEnRef;
    }

    public function calcularGapArbitrageEthBtc(): float
    {
        $precioVentaEth = $this->getRipioBuyEth();
        $precioCompraBtc = $this->getRipioSellBtc();

        $btcPorEthEnRipio = $precioVentaEth / $precioCompraBtc;
        $ethPorBtcEnRef = ($btcPorEthEnRipio / $this->referenceEthBtc) - 1;

        return $ethPorBtcEnRef;
    }

    public function getDolar(): float
    {
        return $this->orderBookDolar->getBestBuyPrice();
    }

    public function getReferenceBtcUsd(): float
    {
        return $this->referenceBtcUsd;
    }

    public function getReferenceEthUsd(): float
    {
        return $this->referenceEthUsd;
    }

    public function getReferenceEthBtc(): float
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
        return $this->orderBookRipioBtcArs->getBestBuyPrice() / ($this->getReferenceBtcUsd() * $this->getDolar()) - 1;
    }

    public function getRipioBuyGapEth(): float
    {
        return $this->orderBookRipioEthArs->getBestBuyPrice() / ($this->getReferenceEthUsd() * $this->getDolar()) - 1;
    }

    public function getRipioSellGapBtc(): float
    {
        return $this->orderBookRipioBtcArs->getBestSellPrice() / ($this->getReferenceBtcUsd() * $this->getDolar()) - 1;
    }

    public function getRipioSellGapEth(): float
    {
        return $this->orderBookRipioEthArs->getBestSellPrice() / ($this->getReferenceEthUsd() * $this->getDolar()) - 1;
    }
}
