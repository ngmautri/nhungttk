<?php
namespace Procure\Domain\Validator;

use Procure\Domain\AbstractDoc;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface HeaderValidatorInterface
{
    /**
     * 
     * @param AbstractDoc $rootEntity
     */
    public function validate(AbstractDoc $rootEntity);
}

