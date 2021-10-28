<?php
namespace Procure\Domain\AccountPayable;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\AccountPayable\Repository\APCmdRepositoryInterface;
use Procure\Domain\AccountPayable\Validator\ValidatorFactory;
use Procure\Domain\Contracts\ProcureDocType;
use Procure\Domain\Event\Ap\ApHeaderCreated;
use Procure\Domain\Service\SharedService;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
final class APDoc extends GenericAP
{

    public function refreshDoc()
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\AccountPayable\GenericAP::specify()
     */
    public function specify()
    {
        $this->setDocType(ProcureDocType::INVOICE);
    }

    /**
     *
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws \RuntimeException
     * @return \Procure\Domain\AccountPayable\APDoc
     */
    public function cloneAndSave(CommandOptions $options, SharedService $sharedService)
    {
        $rows = $this->getDocRows();
        Assert::notNull($rows, "AP Entity is empty!");
        $validationService = ValidatorFactory::create($sharedService);
        Assert::notNull($validationService, "Validation can not created!");

        $instance = new self();

        $exculdedProps = [
            "id",
            "uuid",
            "token",
            "docRows",
            "rowIdArray",
            "instance"
        ];

        /**
         *
         * @var APDoc $instance ;
         */
        $instance = $this->convertExcludeFieldsTo($instance, $exculdedProps);

        // overwrite.
        $instance->initDoc($options);
        $instance->setDocType(ProcureDocType::INVOICE);
        $instance->setBaseDocId($this->getId());
        $instance->setBaseDocType($this->getDocType());
        $instance->validateHeader($validationService->getHeaderValidators());

        $instance->setDocNumber($this->getDocNumber() . "(copied)");
        $instance->setRemarks(\sprintf("Copied from %s", $this->getSysNumber()));

        foreach ($rows as $r) {

            $localEntity = APRow::cloneFrom($instance, $r, $options);
            $instance->addRow($localEntity);
            $instance->validateRow($localEntity, $validationService->getRowValidators());
        }

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getErrorMessage());
        }

        $this->clearEvents();
        /**
         *
         * @var APRowSnapshot $localSnapshot
         * @var APCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->store($instance);

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetDocVersion($rootSnapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new ApHeaderCreated($target, $defaultParams, $params);
        $this->addEvent($event);
        return $instance;
    }

    private static $instance = null;

    // ===================
    private function __construct()
    {}

    /**
     *
     * @return \Procure\Domain\AccountPayable\APDoc
     */
    public static function getInstance()
    {
        return new self();
    }
}