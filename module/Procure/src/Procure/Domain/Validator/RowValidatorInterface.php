<?php
namespace Procure\Domain\Validator;

use Procure\Domain\AbstractDoc;
use Procure\Domain\AbstractRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface RowValidatorInterface
{
    /**
     *
     * @param AbstractDoc $rootEntity
     * @param AbstractRow $localEntity
     */
    public function validate(AbstractDoc $rootEntity, AbstractRow $localEntity);
}

