<?php

namespace App\Util;

use App\Entity\Exchange;
use App\Entity\Orden;
use App\Entity\Rate;
use App\Model\Libro;

class AnalizadorRipio
{
    /** @var Exchange */
    private $exchangeRipio;

    /** @var Exchange */
    private $exchangeReference;

    /** @var Libro */
    private $libroRipioBtcArs;

    /** @var Libro */
    private $libroRipioEthArs;

    /** @var \App\Model\Rate */
    private $referenceUsdArs;

    /** @var Rate */
    private $referenceBtcUsd;

    /** @var Rate */
    private $referenceEthUsd;

    /** @var Rate */
    private $referenceEthBtc;

    public function __construct(Exchange $exchangeRipio, Exchange $exchangeReference, \App\Model\Rate $dolar)
    {
        $this->exchangeRipio = $exchangeRipio;
        $this->exchangeReference = $exchangeReference;

        $this->libroRipioBtcArs = $exchangeRipio->getLibroForPair('BTC/ARS');
        $this->libroRipioEthArs = $exchangeRipio->getLibroForPair('ETH/ARS');

        $this->referenceUsdArs = $dolar;

        $this->referenceBtcUsd = $exchangeReference->getCurrentRateForPair('BTC/USD');
        $this->referenceEthUsd = $exchangeReference->getCurrentRateForPair('ETH/USD');
        $this->referenceEthBtc = $exchangeReference->getCurrentRateForPair('ETH/BTC');

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
        return $this->referenceUsdArs->getSellPrice();
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
        return $this->libroRipioBtcArs->getBestBuyPrice(1000);
    }

    public function getRipioBuyEth(): float
    {
        return $this->libroRipioEthArs->getBestBuyPrice(1000);
    }

    public function getRipioSellBtc(): float
    {
        return $this->libroRipioBtcArs->getBestSellPrice(1000);
    }

    public function getRipioSellEth(): float
    {
        return $this->libroRipioEthArs->getBestSellPrice(1000);
    }

    public function getRipioBuyGapBtc(): float
    {
        return $this->libroRipioBtcArs->getBestBuyPrice() / ($this->getReferenceBtcUsd()->getSellPrice() * $this->getDolar()) - 1;
    }

    public function getRipioBuyGapEth(): float
    {
        return $this->libroRipioEthArs->getBestBuyPrice() / ($this->getReferenceEthUsd()->getSellPrice() * $this->getDolar()) - 1;
    }

    public function getRipioSellGapBtc(): float
    {
        return $this->libroRipioBtcArs->getBestSellPrice() / ($this->getReferenceBtcUsd()->getSellPrice() * $this->getDolar()) - 1;
    }

    public function getRipioSellGapEth(): float
    {
        return $this->libroRipioEthArs->getBestSellPrice() / ($this->getReferenceEthUsd()->getSellPrice() * $this->getDolar()) - 1;
    }

    /**
     * @ignore
     */
    public function getExchangeRipio(): ?Exchange
    {
        return $this->exchangeRipio;
    }

    /**
     * @ignore
     */
    public function setExchangeRipio($exchangeRipio)
    {
        $this->exchangeRipio = $exchangeRipio;
        return $this;
    }

    /**
     * @ignore
     */
    public function getExchangeReference(): ?Exchange
    {
        return $this->exchangeReference;
    }

    /**
     * @ignore
     */
    public function setExchangeReference($exchangeReference)
    {
        $this->exchangeReference = $exchangeReference;
        return $this;
    }
}
