<?php
namespace Application\Domain\Attachment\Contracts;

use Application\Domain\Attachment\GenericAttachment;
use Application\Domain\Attachment\GenericAttachmentFile;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AttachmentFileValidatorCollection implements AttachmentFileValidatorInterface

{

    /**
     *
     * @var $validators[];
     */
    private $validators;

    public function add(AttachmentFileValidatorInterface $validator)
    {
        if (! $validator instanceof AttachmentFileValidatorInterface) {
            throw new InvalidArgumentException("File Validator is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Attachment\Contracts\AttachmentFileValidatorInterface::validate()
     */
    public function validate(GenericAttachment $rootEntity, GenericAttachmentFile $localEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("GenericAttachment Validator is required! but no is given.");
        }

        foreach ($this->validators as $validator) {

            $validator->validate($rootEntity, $localEntity);
        }
    }
}

