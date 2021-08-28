<?php
namespace Inventory\Domain\Service\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface ItemComponentValidationServiceInterface
{

    public function getItemValidators();

    public function getComponentValidators();
}
