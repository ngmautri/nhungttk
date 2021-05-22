<?php
namespace Inventory\Domain\Item\Variant;

use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Closure;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseVariant extends AbstractVariant
{

    private $attributeCollection;

    private $attributeCollectionRef;

    /**
     *
     * @return \Inventory\Domain\Item\Variant\VariantSnapshot
     */
    public function makeSnapshot()
    {
        $snapshot = new VariantSnapshot();
        GenericObjectAssembler::updateAllFieldsFrom($snapshot, $this);
        return $snapshot;
    }

    /**
     *
     * @return mixed
     */
    public function getAttributeCollection()
    {
        return $this->attributeCollection;
    }

    /**
     *
     * @param mixed $attributeCollection
     */
    public function setAttributeCollection($attributeCollection)
    {
        $this->attributeCollection = $attributeCollection;
    }

    /**
     *
     * @return Closure
     */
    public function getAttributeCollectionRef()
    {
        return $this->attributeCollectionRef;
    }

    /**
     *
     * @param Closure $attributeCollectionRef
     */
    public function setAttributeCollectionRef(Closure $attributeCollectionRef)
    {
        $this->attributeCollectionRef = $attributeCollectionRef;
    }
}
