<?php
namespace Procure\Domain\Service\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface TrxValidationServiceInterface
{

    public function getHeaderValidators();

    public function getRowValidators();
}
