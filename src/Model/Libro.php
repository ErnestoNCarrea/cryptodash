<?php

namespace App\Model;

use App\Entity\Orden;
use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Contiene una colección de órdenes.
 */
class Libro
{
    /**
     * El par de las órdenes de este libro, o null si contiene múltiples pares o ninguno.
     **/
    private ?string $par = null;

    /**
     * Las ordenes del libro.
     */
    private $ordenes;

    public function __construct($ordenes, ?string $par = null)
    {
        $this->ordenes = $ordenes;
        $this->par = $par;
    }

    /**
     * Devuelve la mejor oferta de compra o de venta para un par determinado.
     */
    public function obtenerMejorOferta(int $lado, ?string $par = null) : ?Orden
    {
        if ($par === null) {
            $par = $this->par;
        }
        $res = null;

        foreach($this->ordenes as $orden) {
            if($orden->getLado() == $lado && $orden->getPar() == $par) {
                if ($res === null) {
                    // Es la primera orden que se evalúa. Hasta ahora es la mejor.
                    $res = $orden;
                } else {
                    if ($lado == Orden::LADO_VENTA) {
                        if ($orden->getPrecio() < $res->getPrecio()) {
                            // Es mejor que la actual (precio de venta más bajo)
                            $res = $orden;
                        }
                    } elseif($lado == Orden::LADO_COMPRA) {
                        if ($orden->getPrecio() > $res->getPrecio()) {
                            // Es mejor que la actual (precio de compra más alto)
                            $res = $orden;
                        }
                    }
                }
            }
        }

        return $res;
    }

    /**
     * Obtiene una lista completa de todas las divisas en el libro de ordenes.
     * 
     * @return string[]
     */
    public function obtenerTodasLasDivisas(): ?array
    {
        $res = [];

        foreach($this->ordenes as $orden) {
            [ $divisa1, $divisa2 ] = explode('/', $orden->getPar());
            if (in_array($divisa1, $res) == false) {
                $res[] = $divisa1;
            }
            if (in_array($divisa2, $res) == false) {
                $res[] = $divisa2;
            }
        }

        return $res;
    }

    /**
     * Devuelve todas las órdenes del libro.
     */
    public function getOrdenes() : ?array
    {
        return $this->ordenes;
    }

    /**
     * Devuelve solo las ordenes de un lado.
     */
    public function getOrdenesLado(int $lado) : ?array
    {
        $res = [];
        foreach($this->ordenes as $orden) {
            if ($orden->getLado() == $lado) {
                $res[] = $orden;
            }
        }

        return $res;
    }

    /**
     * Devuelve solo las ordenes de compra.
     */
    public function getOrdenesCompra() : ?array
    {
        return $this->getOrdenesLado(Orden::LADO_COMPRA);
    }

    /**
     * Devuelve solo las ordenes de venta.
     */
    public function getOrdenesVenta() : ?array
    {
        return $this->getOrdenesLado(Orden::LADO_VENTA);
    }

    /**
     * Añade una orden al libro.
     */
    public function addOrden(Orden $orden)
    {
        if (is_array($this->ordenes)) {
            $this->ordenes[] = $orden;
        } elseif ($this->ordenes instanceof Collection) {
            $this->ordenes->add($orden);
        } else {
            throw new Exception('No sé cómo añadir un elemento a un ' . get_class($this->ordenes));
        }
    }

    /**
     * Elimina una orden del libro a partir de su id.
     */
    public function elminarOrdenPorId(int $id)
    {
        if (is_array($this->ordenes)) {
            $orden = $this->obtenerOrdenPorId($id);
            if ($orden) {
                $this->ordenes = array_diff($this->ordenes, [ $orden ]);
            }
        } elseif ($this->ordenes instanceof Collection) {
            $this->ordenes->removeElement($orden);
        } else {
            throw new Exception('No sé cómo eliminar un elemento de un ' . get_class($this->ordenes));
        }
    }

    /**
     * Devuelve una orden del libro a partir de su id, o null si no se encuentra una.
     */
    public function obtenerOrdenPorId(int $id) : ?Orden
    {
        foreach($this->ordenes as $orden) {
            if ($orden->getId() == $id) {
                return $orden;
            }
        }

        return null;
    }

    public function getMejorOrdenCompra() : ?Orden
    {
        if ($this->par === null) {
            return null;
        }
        return $this->obtenerMejorOferta(Orden::LADO_COMPRA);
    }

    public function getMejorOrdenVenta(): ?Orden
    {
        if ($this->par === null) {
            return null;
        }
        return $this->obtenerMejorOferta(Orden::LADO_VENTA);
    }

    public function getMejorPrecioCompra(): ?float
    {
        $orden = $this->getMejorOrdenCompra();
        if ($orden === null) {
            return null;
        } else {
            return $orden->getPrecio();
        }
    }

    public function getMejorPrecioVenta(): ?float
    {
        $orden = $this->getMejorOrdenVenta();
        if ($orden === null) {
            return null;
        } else {
            return $orden->getPrecio();
        }
    }

    /**
     * @ignore
     */ 
    public function getPar() : ?string
    {
        return $this->par;
    }

    /**
     * @ignore
     */ 
    public function setPar(?string $par): self
    {
        $this->par = $par;

        return $this;
    }

    /**
     * @ignore
     */ 
    public function count(): int
    {
        return count($this->ordenes);
    }
   
}
