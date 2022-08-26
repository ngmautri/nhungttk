<?php
namespace Application\Domain\Attachment\Validator;

use Application\Domain\Attachment\Service\AttachmentValidationService;
use Application\Domain\Attachment\Validator\Contracts\AttachmentValidatorCollection;
use Application\Domain\Service\Contracts\SharedServiceInterface;
use Webmozart\Assert\Assert;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AttachmentValidatorFactory
{

    public static function forCreatingAttachment(SharedServiceInterface $sharedService, $isPosting = false)
    {
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($sharedService->getSharedSpecificationFactory() == null) {
            throw new InvalidArgumentException("Shared spec service not found");
        }

        $sharedSpecsFactory = $sharedService->getSharedSpecificationFactory();

        $attachmentValidators = new AttachmentValidatorCollection();
        $validator = new AttachmentDefaultValidator($sharedSpecsFactory);
        $attachmentValidators->add($validator);

        $attachmentFileValidators = null;
        Assert::notNull($attachmentValidators, "Attachment validator is null");

        return new AttachmentValidationService($attachmentValidators, $attachmentFileValidators);
    }
}