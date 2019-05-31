<?php
namespace Application\Application\Specification\Zend;

use Application\Domain\Shared\Specification\AbstractSpecificationFactory;
use Application\Application\Specification\Zend\EmailSpecification;
use Application\Application\Specification\Zend\DateSpecification;
use Application\Application\Specification\Zend\NullorBlankSpecification;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ZendSpecificationFactory extends AbstractSpecificationFactory
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getEmailSpecification()
     */
    public function getEmailSpecification()
    {
        return new EmailSpecification();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getDateSpecification()
     */
    public function getDateSpecification()
    {
        return new DateSpecification();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getNullorBlankSpecification()
     */
    public function getNullorBlankSpecification()
    {
        return new NullorBlankSpecification();
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getPositiveNumberSpecification()
     */
    public function getPositiveNumberSpecification()
    {
        return new PositiveNumberSpecification();
        
    }

}