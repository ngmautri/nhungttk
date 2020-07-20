<?php
namespace Application\Domain\Util\Composite\Validator;

use Application\Domain\Util\Composite\AbstractBaseComponent;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface CompositeValidatorInterface
{

    /**
     *
     * @param AbstractBaseComponent $component
     */
    public function validate(AbstractBaseComponent $component);
}

