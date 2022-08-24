<?php
namespace Application\Domain\Attachment\Validator\Contracts;

use Application\Domain\Attachment\GenericAttachment;
use Application\Domain\Company\Brand\Validator\Contracts\AttachmentValidatorInterface;
use Application\Domain\Company\Brand\Validator\Contracts\BrandValidatorInterface;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AttachmentValidatorCollection implements BrandValidatorInterface

{

    /**
     *
     * @var $validators[];
     */
    private $validators;

    public function add(AttachmentValidatorInterface $validator)
    {
        if (! $validator instanceof BrandValidatorInterface) {
            throw new InvalidArgumentException("AttachmentValidatorInterface is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\Brand\Validator\Contracts\BrandValidatorInterface::validate()
     */
    public function validate(GenericAttachment $rootEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("GenericAttachment is required! but no is given.");
        }

        foreach ($this->validators as $validator) {
            $validator->validate($rootEntity);
        }
    }
}

