<?php
namespace Application\Application\Specification\InMemory;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Zend\Validator\Date;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class DateSpecification extends AbstractSpecification
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        $validator = new Date();
        return $validator->isValid($subject);
    }
}