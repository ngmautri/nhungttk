<?php
namespace Inventory\Domain\Service\Contracts;

use Procure\Domain\Service\Contracts\ValidationServiceInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface TrxValidationServiceInterface extends ValidationServiceInterface
{

    public function getHeaderValidators();

    public function getRowValidators();
}
