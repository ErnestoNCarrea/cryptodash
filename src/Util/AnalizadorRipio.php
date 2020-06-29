<?php

namespace App\Util;

use App\Entity\Exchange;
use App\Entity\Orden;
use App\Entity\Cotizacion;
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

    /** @var \App\Entity\Cotizacion */
    private $referenceUsdArs;

    /** @var Cotizacion */
    private $referenceBtcUsd;

    /** @var Cotizacion */
    private $referenceEthUsd;

    /** @var Cotizacion */
    private $referenceEthBtc;

    public function __construct(Exchange $exchangeRipio, Exchange $exchangeReference, \App\Entity\Cotizacion $dolar)
    {
        $this->exchangeRipio = $exchangeRipio;
        $this->exchangeReference = $exchangeReference;

        $this->libroRipioBtcArs = $exchangeRipio->getLibroForPar('BTC/ARS');
        $this->libroRipioEthArs = $exchangeRipio->getLibroForPar('ETH/ARS');

        $this->referenceUsdArs = $dolar;

        $this->referenceBtcUsd = $exchangeReference->getCotizacionForPar('BTC/USD');
        $this->referenceEthUsd = $exchangeReference->getCotizacionForPar('ETH/USD');
        $this->referenceEthBtc = $exchangeReference->getCotizacionForPar('ETH/BTC');

    }

    public function calcularGapArbitrageBtcEth(): float
    {
        $precioVentaBtc = $this->getRipioBuyBtc();
        $precioCompraEth = $this->getRipioSellEth();

        $ethPorBtcEnRipio = $precioVentaBtc / $precioCompraEth;
        $btcPorEthEnRef = ($ethPorBtcEnRipio * $this->referenceEthBtc->getPrecioVenta()) - 1;

        return $btcPorEthEnRef;
    }

    public function calcularGapArbitrageEthBtc(): float
    {
        $precioVentaEth = $this->getRipioBuyEth();
        $precioCompraBtc = $this->getRipioSellBtc();

        $btcPorEthEnRipio = $precioVentaEth / $precioCompraBtc;
        $ethPorBtcEnRef = ($btcPorEthEnRipio / $this->referenceEthBtc->getPrecioVenta()) - 1;

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
        return $this->referenceUsdArs->getPrecioVenta();
    }

    public function getReferenceBtcUsd(): Cotizacion
    {
        return $this->referenceBtcUsd;
    }

    public function getReferenceEthUsd(): Cotizacion
    {
        return $this->referenceEthUsd;
    }

    public function getReferenceEthBtc(): Cotizacion
    {
        return $this->referenceEthBtc;
    }

    public function getRipioBuyBtc(): float
    {
        return $this->libroRipioBtcArs->getBestPrecioCompra(1000);
    }

    public function getRipioBuyEth(): float
    {
        return $this->libroRipioEthArs->getBestPrecioCompra(1000);
    }

    public function getRipioSellBtc(): float
    {
        return $this->libroRipioBtcArs->getBestPrecioVenta(1000);
    }

    public function getRipioSellEth(): float
    {
        return $this->libroRipioEthArs->getBestPrecioVenta(1000);
    }

    public function getRipioBuyGapBtc(): float
    {
        return $this->libroRipioBtcArs->getBestPrecioCompra() / ($this->getReferenceBtcUsd()->getPrecioVenta() * $this->getDolar()) - 1;
    }

    public function getRipioBuyGapEth(): float
    {
        return $this->libroRipioEthArs->getBestPrecioCompra() / ($this->getReferenceEthUsd()->getPrecioVenta() * $this->getDolar()) - 1;
    }

    public function getRipioSellGapBtc(): float
    {
        return $this->libroRipioBtcArs->getBestPrecioVenta() / ($this->getReferenceBtcUsd()->getPrecioVenta() * $this->getDolar()) - 1;
    }

    public function getRipioSellGapEth(): float
    {
        return $this->libroRipioEthArs->getBestPrecioVenta() / ($this->getReferenceEthUsd()->getPrecioVenta() * $this->getDolar()) - 1;
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
