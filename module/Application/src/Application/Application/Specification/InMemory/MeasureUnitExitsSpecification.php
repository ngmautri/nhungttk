<?php
namespace Application\Application\Specification\InMemory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class MeasureUnitExitsSpecification extends AbstractInMemorySpecification
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        return false;
    }
}