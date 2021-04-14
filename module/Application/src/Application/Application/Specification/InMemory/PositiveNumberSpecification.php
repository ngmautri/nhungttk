<?php
namespace Application\Application\Specification\InMemory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PositiveNumberSpecification extends AbstractInMemorySpecification
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        if (! is_numeric($subject))
            return false;

        return ($subject > 0);
    }
}