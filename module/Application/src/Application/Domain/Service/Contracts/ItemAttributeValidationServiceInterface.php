<?php
namespace Application\Domain\Service\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface ItemAttributeValidationServiceInterface
{

    public function getAttributeGroupValidators();

    public function getAttributeValidators();
}
