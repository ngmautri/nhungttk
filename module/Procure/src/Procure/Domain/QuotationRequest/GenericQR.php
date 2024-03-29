<?php
namespace Procure\Domain\QuotationRequest;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Domain\Util\Translator;
use Procure\Application\DTO\Qr\QrDTO;
use Procure\Domain\Contracts\ProcureDocStatus;
use Procure\Domain\Event\Qr\QrPosted;
use Procure\Domain\Event\Qr\QrRowAdded;
use Procure\Domain\Event\Qr\QrRowRemoved;
use Procure\Domain\Event\Qr\QrRowUpdated;
use Procure\Domain\QuotationRequest\Repository\QrCmdRepositoryInterface;
use Procure\Domain\QuotationRequest\Validator\ValidatorFactory;
use Procure\Domain\Service\QrPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Service\Contracts\SharedServiceInterface;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Procure\Infrastructure\Doctrine\QRCmdRepositoryImpl;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GenericQR extends AbstractQR
{

    /**
     *
     * @param SharedService $sharedService
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return \Procure\Domain\QuotationRequest\GenericQR
     */
    public function store(SharedService $sharedService)
    {
        Assert::notNull($sharedService, Translator::translate(sprintf("Shared Service not set! %s", __FUNCTION__)));

        $rep = $sharedService->getPostingService()->getCmdRepository();

        if (! $rep instanceof QRCmdRepositoryImpl) {
            throw new \InvalidArgumentException(Translator::translate(sprintf("QRCmdRepositoryImpl not set! %s", __FUNCTION__)));
        }

        $this->setLogger($sharedService->getLogger());
        $validationService = ValidatorFactory::create($sharedService, true);

        $this->validate($validationService);
        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $rep->store($this);

        $this->logInfo(\sprintf("Quotation saved %s", __METHOD__));
        return $this;
    }

    public function deactivateRow(QRRow $row, CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, QrPostingService $postingService)
    {}

    /**
     *
     * @param QrRow $row
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @return \Procure\Domain\QuotationRequest\QRRowSnapshot
     */
    public function removeRow(QrRow $row, CommandOptions $options, SharedService $sharedService)
    {
        Assert::notEq($this->getDocStatus(), ProcureDocStatus::POSTED, sprintf("GR is posted already! %s", $this->getId()));
        Assert::notNull($options, "command options not found");

        /**
         *
         * @var QRRowSnapshot $localSnapshot
         * @var QRCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->removeRow($this, $row);

        $params = [
            "rowId" => $row->getId(),
            "rowToken" => $row->getToken()
        ];

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new QrRowRemoved($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    /**
     *
     * @param QRRowSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedServiceInterface $sharedService
     * @throws \RuntimeException
     * @return \Procure\Domain\QuotationRequest\QRRowSnapshot
     */
    public function createRowFrom(QRRowSnapshot $snapshot, CommandOptions $options, SharedServiceInterface $sharedService, $storeNow = true)
    {
        Assert::notEq($this->getDocStatus(), ProcureDocStatus::POSTED, sprintf("QR is posted!%s", $this->getId()));
        Assert::notNull($options, "command options not found");
        Assert::notNull($snapshot, "QRRowSnapshot not found");

        $validationService = ValidatorFactory::create($sharedService);

        $snapshot->docType = $this->docType;

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->initSnapshot($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $row = QRRow::createFromSnapshot($this, $snapshot);

        $this->validateRow($row, $validationService->getRowValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->clearEvents();
        $this->addRow($row);

        if (! $storeNow) {
            return $this;
        }

        /**
         *
         * @var QRRowSnapshot $localSnapshot
         * @var QrCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeRow($this, $row);

        $params = [
            "rowId" => $localSnapshot->getId(),
            "rowToken" => $localSnapshot->getToken()
        ];

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $event = new QrRowAdded($target, $defaultParams, $params);

        $this->addEvent($event);

        return $localSnapshot;
    }

    /**
     *
     * @param QRRowSnapshot $snapshot
     * @param CommandOptions $options
     * @param array $params
     * @param SharedServiceInterface $sharedService
     * @throws \RuntimeException
     * @return \Procure\Domain\QuotationRequest\QRRowSnapshot
     */
    public function updateRowFrom(QRRowSnapshot $snapshot, CommandOptions $options, $params, SharedServiceInterface $sharedService)
    {
        Assert::notEq($this->getDocStatus(), ProcureDocStatus::POSTED, sprintf("QR is posted!%s", $this->getId()));
        Assert::notNull($options, "command options not found");
        Assert::notNull($snapshot, "QRRowSnapshot not found");

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->markAsChange($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
        $validationService = ValidatorFactory::create($sharedService);

        $row = QRRow::createFromSnapshot($this, $snapshot);
        $this->validateRow($row, $validationService->getRowValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->clearEvents();

        /**
         *
         * @var QRRowSnapshot $localSnapshot
         * @var QrCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeRow($this, $row);

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $event = new QrRowUpdated($target, $defaultParams, $params);

        $this->addEvent($event);

        return $localSnapshot;
    }

    /**
     *
     * @param CommandOptions $options
     * @param SharedServiceInterface $sharedService
     * @throws \RuntimeException
     * @return \Procure\Domain\QuotationRequest\GenericQR
     */
    public function post(CommandOptions $options, SharedServiceInterface $sharedService)
    {
        Assert::notEq($this->getDocStatus(), ProcureDocStatus::POSTED, sprintf("QR is already posted!%s", $this->getId()));
        Assert::notNull($options, "command options not found");
        $validationService = ValidatorFactory::create($sharedService);

        $this->validate($validationService);
        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $this->clearEvents();

        $this->prePost($options, $validationService, $sharedService);
        $this->doPost($options, $validationService, $sharedService);
        $this->afterPost($options, $validationService, $sharedService);

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new QrPosted($target, $defaultParams, $params);

        $this->addEvent($event);
        return $this;
    }

    /**
     *
     * @param CommandOptions $options
     * @param SharedServiceInterface $sharedService
     * @throws \RuntimeException
     * @return \Procure\Domain\QuotationRequest\GenericQR
     */
    public function reverse(CommandOptions $options, SharedServiceInterface $sharedService)
    {
        Assert::eq($this->getDocStatus(), ProcureDocStatus::POSTED, sprintf("QR is not posted!%s", $this->getId()));
        Assert::notNull($options, "command options not found");

        $validationService = ValidatorFactory::create($sharedService);
        $this->validate($validationService);
        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $this->clearEvents();

        $this->preReserve($options, $validationService, $sharedService);
        $this->doReverse($options, $validationService, $sharedService);
        $this->afterReserve($options, $validationService, $sharedService);

        $this->addEvent();
        return $this;
    }

    /**
     *
     * @return NULL|object
     */
    public function makeDetailsDTO()
    {
        $dto = new QrDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @return NULL|object
     */
    public function makeHeaderDTO()
    {
        $dto = new QrDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::makeDTOForGrid()
     */
    public function makeDTOForGrid()
    {
        $dto = new QRSnapshot();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        $rowDTOList = [];
        if (count($this->docRows) > 0) {
            foreach ($this->docRows as $row) {

                if ($row instanceof QRRow) {
                    $rowDTOList[] = $row->makeDetailsDTO();
                }
            }
        }

        $dto->docRowsDTO = $rowDTOList;
        return $dto;
    }
}