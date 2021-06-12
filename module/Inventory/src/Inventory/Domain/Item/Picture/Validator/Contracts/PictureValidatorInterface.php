<?php
namespace Inventory\Domain\Item\Picture\Validator\Contracts;

use Inventory\Domain\Item\Picture\BasePicture;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface PictureValidatorInterface
{

    public function validate(BasePicture $rootEntity);
}

