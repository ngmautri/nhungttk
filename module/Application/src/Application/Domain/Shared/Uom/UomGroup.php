<?php
namespace Application\Domain\Shared\Uom;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class UomGroup implements \JsonSerializable
{
    private $uom;
    private $baseUom;
    private $convertFactor;

    /**
     *
     * @param Uom $uom
     * @param Uom $baseUom
     * @param int $convertFactor
     */
    public function __construct(Uom $uom, Uom $baseUom, $convertFactor){

        if($uom instanceof Uom){
            throw new \InvalidArgumentException("Invalid argument");
        }


        if($baseUom instanceof Uom){
            throw new \InvalidArgumentException("Invalid argument");
        }

        if(!\is_numeric($convertFactor)){
            throw new \InvalidArgumentException("Invalid argument");
        }

        if($this->uom== $this->baseUom){

        }


        $this->uom = $uom;
        $this->baseUom = $baseUom;
        $this->convertFactor = $convertFactor;
    }
    public function jsonSerialize()
    {}


}
