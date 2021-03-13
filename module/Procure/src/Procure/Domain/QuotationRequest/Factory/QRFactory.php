<?php
namespace Procure\Domain\QuotationRequest\Factory;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\Event\Qr\QrHeaderCreated;
use Procure\Domain\Event\Qr\QrHeaderUpdated;
use Procure\Domain\QuotationRequest\GenericQR;
use Procure\Domain\QuotationRequest\QRDoc;
use Procure\Domain\QuotationRequest\QRSnapshot;
use Procure\Domain\QuotationRequest\QRSnapshotAssembler;
use Procure\Domain\QuotationRequest\Repository\QrCmdRepositoryInterface;
use Procure\Domain\QuotationRequest\Validator\ValidatorFactory;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Shared\ProcureDocStatus;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class QRFactory
{

    public static function createEmptyObject($docType)
    {
        $instance = self::createDoc($docType);
        return $instance;
    }

    public static function constructFromDB(QRSnapshot $snapshot)
    {
        if (! $snapshot instanceof QRSnapshot) {
            return null;
        }

        $docType = $snapshot->getDocType();
        $instance = self::createDoc($docType);
        QRSnapshotAssembler::updateEntityAllFieldsFrom($instance, $snapshot);
        return $instance;
    }

    /**
     *
     * @param QRSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws \RuntimeException
     * @return \Procure\Domain\QuotationRequest\QRDoc
     */
    public static function createFrom(QRSnapshot $snapshot, CommandOptions $options, SharedService $sharedService)
    {
        Assert::notNull($snapshot, "QR snapshot not found");
        Assert::notNull($options, "Command options not found");

        $docType = $snapshot->getDocType();
        $instance = self::createDoc($docType);

        $snapshot->initDoc($options);

        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $snapshot->setExchangeRate($fxRate);

        QRSnapshotAssembler::updateEntityAllFieldsFrom($instance, $snapshot);

        $validationService = ValidatorFactory::create($sharedService);
        $instance->validateHeader($validationService->getHeaderValidators());

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getNotification()->errorMessage());
        }

        $instance->clearEvents();

        /**
         *
         * @var QRSnapshot $rootSnapshot
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->storeHeader($instance, false);
        $instance->updateIdentityFrom($rootSnapshot);

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetDocVersion($rootSnapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new QrHeaderCreated($target, $defaultParams, $params);
        $instance->addEvent($event);

        return $instance;
    }

    public static function updateFrom(GenericQR $rootEntity, QRSnapshot $snapshot, CommandOptions $options, $params, SharedService $sharedService)
    {
        Assert::notNull($rootEntity, sprintf("Root entity not found!"));
        Assert::notEq($rootEntity->getDocStatus(), ProcureDocStatus::POSTED, sprintf("GR is already posted! %s", $rootEntity->getId()));
        Assert::notNull($snapshot, "GR snapshot not found");
        Assert::notNull($options, "Command options not found");

        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $snapshot->setExchangeRate($fxRate);

        // SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        QRSnapshotAssembler::updateEntityExcludedDefaultFieldsFrom($rootEntity, $snapshot);

        $validationService = ValidatorFactory::create($sharedService);
        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $rootEntity->markDocAsChanged($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
        $rootEntity->validateHeader($validationService->getHeaderValidators());

        if ($rootEntity->hasErrors()) {
            throw new \RuntimeException(sprintf("%s-%s", $rootEntity->getNotification()->errorMessage(), __FUNCTION__));
        }

        $rootEntity->clearEvents();

        /**
         *
         * @var QRSnapshot $rootSnapshot
         * @var QrCmdRepositoryInterface $rep ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->storeHeader($rootEntity);

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetDocVersion($rootSnapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new QrHeaderUpdated($target, $defaultParams, $params);
        $rootEntity->addEvent($event);

        return $rootEntity;
    }

    /**
     *
     * @param int $docType
     * @return \Procure\Domain\QuotationRequest\QRDoc
     */
    private static function createDoc($docType)
    {
        $doc = QRDoc::getInstance();
        Assert::notNull($doc);

        return $doc;
    }
}