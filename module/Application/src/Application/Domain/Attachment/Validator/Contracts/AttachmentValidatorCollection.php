<?php
namespace Application\Domain\Attachment\Validator\Contracts;

use Application\Domain\Attachment\BaseAttachment;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AttachmentValidatorCollection implements AttachmentValidatorInterface

{

    /**
     *
     * @var $validators[];
     */
    private $validators;

    public function add(AttachmentValidatorInterface $validator)
    {
        if (! $validator instanceof AttachmentValidatorInterface) {
            throw new InvalidArgumentException("AttachmentValidatorInterface is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\Brand\Validator\Contracts\BrandValidatorInterface::validate()
     */
    public function validate(BaseAttachment $rootEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("GenericAttachment is required! but no is given.");
        }

        foreach ($this->validators as $validator) {
            $validator->validate($rootEntity);
        }
    }
}

