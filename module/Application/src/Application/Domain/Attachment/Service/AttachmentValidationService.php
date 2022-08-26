<?php
namespace Application\Domain\Attachment\Service;

use Application\Domain\Attachment\Contracts\AttachmentFileValidatorCollection;
use Application\Domain\Attachment\Validator\Contracts\AttachmentValidatorCollection;
use Inventory\Domain\Exception\InvalidArgumentException;

/**
 * Transaction Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AttachmentValidationService implements AttachmentValidationServiceInterface
{

    protected $attachmentValidators;

    protected $attachmentFileValidators;

    public function __construct(AttachmentValidatorCollection $attachmentValidators, AttachmentFileValidatorCollection $attachmentFileValidators = null)
    {
        if ($attachmentValidators == null) {
            throw new InvalidArgumentException("Attachment Validator(s) is required");
        }

        $this->attachmentValidators = $attachmentValidators;
        $this->attachmentFileValidators = $attachmentFileValidators;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Attachment\Service\AttachmentValidationServiceInterface::getAttachmentValidators()
     */
    public function getAttachmentValidators()
    {
        return $this->attachmentValidators;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Attachment\Service\AttachmentValidationServiceInterface::getAttachmentFileValidators()
     */
    public function getAttachmentFileValidators()
    {
        return $this->attachmentFileValidators;
    }
}