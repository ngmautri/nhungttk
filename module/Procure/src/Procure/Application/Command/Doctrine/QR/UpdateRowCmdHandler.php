<?php
namespace Procure\Application\Command\Doctrine\QR;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Doctrine\VersionChecker;
use Procure\Application\Command\Options\UpdateRowCmdOptions;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Application\Service\QR\QRRowSnapshotModifier;
use Procure\Domain\QuotationRequest\QRDoc;
use Procure\Domain\QuotationRequest\QRRow;
use Procure\Domain\QuotationRequest\QRRowSnapshot;
use Procure\Domain\QuotationRequest\QRRowSnapshotAssembler;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UpdateRowCmdHandler extends AbstractCommandHandler
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Command\CommandHandlerInterface::run()
     */
    public function run(CommandInterface $cmd)
    {
        /**
         *
         * @var QRDoc $rootEntity ;
         * @var UpdateRowCmdOptions $options ;
         * @var AbstractCommand $cmd ;
         * @var QRRowSnapshot $snapshot ;
         * @var QRRowSnapshot $newSnapshot ;
         * @var QRRow $row ;
         *
         *
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);
        Assert::notNull($cmd->getData(), 'Input data emty!');
        Assert::isInstanceOf($cmd->getOptions(), UpdateRowCmdOptions::class);
        $options = $cmd->getOptions();

        $rootEntity = $options->getRootEntity();
        Assert::isInstanceOf($rootEntity, QRDoc::class);

        $localEntity = $options->getLocalEntity();
        Assert::isInstanceOf($localEntity, QRRow::class);

        try {

            $row = $localEntity;
            $snapshot = $row->makeSnapshot();
            $newSnapshot = clone ($snapshot);

            $newSnapshot = QRRowSnapshotAssembler::updateDefaultIncludedFieldsFromArray($newSnapshot, $cmd->getData());
            $this->setOutput($newSnapshot);

            $newSnapshot = QRRowSnapshotModifier::modify($newSnapshot, $cmd->getDoctrineEM(), $options->getLocale());

            $changeLog = $snapshot->compare($newSnapshot);

            if ($changeLog == null) {
                $cmd->addError("Nothing change on QR#" . $rootEntity->getId());
                return;
            }

            $params = [
                "rowId" => $row->getId(),
                "rowToken" => $row->getToken(),
                "changeLog" => $changeLog
            ];

            $sharedService = SharedServiceFactory::createForQR($cmd->getDoctrineEM());
            $rootEntity->updateRowFrom($newSnapshot, $options, $params, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================
            // Check Version
            // ==============
            VersionChecker::checkQRVersion($cmd->getDoctrineEM(), $rootEntity->getId(), $options->getVersion());
            // ===============

            $cmd->addSuccess(\sprintf("Quotation #%s updated", $rootEntity->getId()));
        } catch (\Exception $e) {
            $cmd->addError($e->getMessage());

            throw new \RuntimeException($e->getMessage());
        }
    }
}
