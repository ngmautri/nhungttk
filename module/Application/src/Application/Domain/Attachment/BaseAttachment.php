<?php
namespace Application\Domain\Attachment;

use Application\Domain\Attachment\Service\AttachmentValidationServiceInterface;
use Application\Domain\Company\Brand\BaseBrand;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class BaseAttachment extends AbstractAttachment
{

    public function validateAttachment(AttachmentValidationServiceInterface $validatorService, $isPosting = false)
    {
        $validators = $validatorService->getAttachmentValidators();
        $validators->validate($this);

        if ($this->hasErrors()) {
            $this->addErrorArray($this->getErrors());
        }
    }

    public function equals(BaseBrand $other)
    {}

    public function makeSnapshot()
    {
        $snapshot = new BaseAttachmentSnapshot();
        GenericObjectAssembler::updateAllFieldsFrom($snapshot, $this);
        return $snapshot;
    }
}