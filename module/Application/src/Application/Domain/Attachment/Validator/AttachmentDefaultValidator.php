<?php
namespace Application\Domain\Attachment\Validator;

use Application\Domain\Attachment\BaseAttachment;
use Application\Domain\Attachment\GenericAttachment;
use Application\Domain\Attachment\Validator\Contracts\AttachmentValidatorInterface;
use Application\Domain\Company\Validator\Contracts\AbstractValidator;
use Application\Domain\Util\OutputMessage;
use Application\Domain\Util\Translator;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AttachmentDefaultValidator extends AbstractValidator implements AttachmentValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Attachment\Validator\Contracts\AttachmentValidatorInterface::validate()
     */
    public function validate(BaseAttachment $rootEntity)
    {
        if (! $rootEntity instanceof GenericAttachment) {
            $rootEntity->addError("GenericAttachment object not found");
            return;
        }

        try {

            $spec = $this->getSharedSpecificationFactory()->getNullorBlankSpecification();
            if ($spec->isSatisfiedBy($rootEntity->getSubject())) {
                $m = Translator::translate("Attachment subject empty");
                $rootEntity->addError(OutputMessage::error($m, ($rootEntity->getSubject())));
            }

            // User
            $spec = $this->getSharedSpecificationFactory()->getUserExitsSpecification();

            // Created by
            if ($rootEntity->getCreatedBy() > 0) {

                $subject = array(
                    "userId" => $rootEntity->getCreatedBy()
                );

                if (! $spec->isSatisfiedBy($subject)) {
                    $rootEntity->addError(\sprintf("User can not be identified. #%s, %s ", $rootEntity->getCreatedBy(), __CLASS__));
                }
            }

            // Last Change by
            if ($rootEntity->getLastChangeBy() > 0) {

                $subject = array(
                    "userId" => $rootEntity->getLastChangeBy()
                );

                if (! $spec->isSatisfiedBy($subject)) {
                    $rootEntity->addError("User can not be identified. #" . $rootEntity->getLastChangeBy());
                }
            }
        } catch (Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}