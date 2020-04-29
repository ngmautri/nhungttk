<?php
namespace Procure\Application\Command\QR;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\QR\Options\RowCreateOptions;
use Procure\Application\DTO\Qr\QrRowDTO;
use Procure\Application\Service\FXService;
use Procure\Application\Service\QR\RowSnapshotReference;
use Procure\Domain\Exception\DBUpdateConcurrencyException;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\QuotationRequest\QRDoc;
use Procure\Domain\QuotationRequest\QRRowSnapshot;
use Procure\Domain\QuotationRequest\Validator\Header\DefaultHeaderValidator;
use Procure\Domain\QuotationRequest\Validator\Row\DefaultRowValidator;
use Procure\Domain\Service\QrPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Procure\Infrastructure\Doctrine\QRCmdRepositoryImpl;
use Procure\Infrastructure\Doctrine\QRQueryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AddRowCmdHandler extends AbstractCommandHandler
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Command\AbstractDoctrineCmdHandler::run()
     */
    public function run(CommandInterface $cmd)
    {
        if (! $cmd instanceof AbstractDoctrineCmd) {
            throw new InvalidArgumentException(sprintf("% not found!", "AbstractDoctrineCmd"));
        }

        if (! $cmd->getDto() instanceof QrRowDTO) {
            throw new InvalidArgumentException("QrRowDTO object not found!");
        }

        if (! $cmd->getOptions() instanceof RowCreateOptions) {
            throw new InvalidArgumentException("Cmd Options object not found!");
        }

        /**
         *
         * @var QrRowDTO $dto ;
         * @var InvalidArgumentException $options ;
         * @var QRRowSnapshot $snapshot ;
         * @var QRDoc $rootEntity ;
         */
        $dto = $cmd->getDto();
        $options = $cmd->getOptions();

        $notification = new Notification();

        $userId = $options->getUserId();
        $rootEntity = $options->getRootEntity();
        $version = $options->getVersion();

        try {

            $dto->createdBy = $userId;
            $dto->company = $rootEntity->getCompany();

            $snapshot = SnapshotAssembler::createSnapShotFromArray($dto, new QRRowSnapshot());
            $snapshot = RowSnapshotReference::updateReferrence($snapshot, $cmd->getDoctrineEM());

            $sharedSpecificationFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $fxService = new FXService();
            $sharedService = new SharedService($sharedSpecificationFactory, $fxService);

            $headerValidators = new HeaderValidatorCollection();
            $validator = new DefaultHeaderValidator($sharedSpecificationFactory, $fxService);
            $headerValidators->add($validator);

            $rowValidators = new RowValidatorCollection();
            $validator = new DefaultRowValidator($sharedSpecificationFactory, $fxService);
            $rowValidators->add($validator);

            $cmdRepository = new QRCmdRepositoryImpl($cmd->getDoctrineEM());
            $postingService = new QrPostingService($cmdRepository);

            $localSnapshot = $rootEntity->createRowFrom($snapshot, $options, $headerValidators, $rowValidators, $sharedService, $postingService);

            // event dispatch
            // event dispatcher
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }

            $m = sprintf("[OK] Row # %s created", $localSnapshot->getId());
            $notification->addSuccess($m);

            $queryRep = new QRQueryRepositoryImpl($cmd->getDoctrineEM());

            $dto->setNotification($notification);

            // revision numner has been increased.
            $currentVersion = $queryRep->getVersion($rootEntity->getId()) - 1;
            if ($version != $currentVersion) {
                throw new DBUpdateConcurrencyException(sprintf("Object has been changed from %s to %s since retrieving. Please retry! ", $version, $currentVersion));
            }
        } catch (\Exception $e) {
            throw new OperationFailedException($e->getMessage());
        }
    }
}
