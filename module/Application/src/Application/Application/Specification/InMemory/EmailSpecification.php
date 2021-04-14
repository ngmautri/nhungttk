<?php
namespace Application\Application\Specification\InMemory;

use Application\Domain\Shared\Specification\AbstractSpecification;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class EmailSpecification extends AbstractSpecification
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