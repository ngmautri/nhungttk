<?php
namespace Inventory\Domain\Service\Contracts;

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