<?php
namespace Inventory\Domain\Item\Picture\Validator\Contracts;

use Inventory\Domain\Item\Picture\BasePicture;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PictureValidatorCollection implements PictureValidatorInterface
{

    /**
     *
     * @var $validators[];
     */
    private $validators;

    public function add(PictureValidatorInterface $validator)
    {
        if (! $validator instanceof PictureValidatorInterface) {
            throw new InvalidArgumentException("PictureValidatorInterface Validator is required!");
        }

        $this->validators[] = $validator;
    }

    public function validate(BasePicture $rootEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("Header Validator is required! but no is given.");
        }

        foreach ($this->validators as $validator) {
            $validator->validate($rootEntity);
        }
    }
}

