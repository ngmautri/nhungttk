<?php
namespace Application\Application\Specification\Zend;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Zend\Validator\EmailAddress;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class NullorBlankSpecification extends AbstractSpecification
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        return ($subject == null || $subject == "");
    }
}