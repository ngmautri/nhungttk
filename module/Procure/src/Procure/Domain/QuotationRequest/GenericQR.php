<?php
namespace Procure\Domain\QuotationRequest;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Service\SharedServiceInterface;
use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Domain\Util\Translator;
use Procure\Application\DTO\Qr\QrDTO;
use Procure\Domain\AccountPayable\AbstractAP;
use Procure\Domain\Contracts\ProcureDocStatus;
use Procure\Domain\Event\Qr\QrPosted;
use Procure\Domain\Event\Qr\QrRowAdded;
use Procure\Domain\Event\Qr\QrRowUpdated;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\InvalidOperationException;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\Exception\ValidationFailedException;
use Procure\Domain\QuotationRequest\Repository\QrCmdRepositoryInterface;
use Procure\Domain\QuotationRequest\Validator\ValidatorFactory;
use Procure\Domain\Service\QrPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class GenericQR extends AbstractAP
{

    public function deactivateRow(QRRow $row, CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, QrPostingService $postingService)
    {}

    /**
     *
     * @param QRRowSnapshot $snapshot
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param SharedService $sharedService
     * @param QrPostingService $postingService
     * @throws InvalidOperationException
     * @throws InvalidArgumentException
     * @throws ValidationFailedException
     * @throws OperationFailedException
     * @return \Procure\Domain\QuotationRequest\QRRowSnapshot
     */
    public function createRowFrom(QRRowSnapshot $snapshot, CommandOptions $options, SharedService $sharedService)
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
     * @param arraay $params
     * @param SharedService $sharedService
     * @throws \RuntimeException
     * @return \Procure\Domain\QuotationRequest\QRRowSnapshot
     */
    public function updateRowFrom(QRRowSnapshot $snapshot, CommandOptions $options, $params, SharedService $sharedService)
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
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param SharedService $sharedService
     * @param QrPostingService $postingService
     * @throws InvalidOperationException
     * @throws ValidationFailedException
     * @return \Procure\Domain\QuotationRequest\GenericQR
     */
    public function reverse(CommandOptions $options, SharedService $sharedService)
    {
        if ($this->getDocStatus() !== ProcureDocStatus::POSTED) {
            throw new InvalidOperationException(Translator::translate(sprintf("Document is not posted yet! %s", __METHOD__)));
        }
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

    /**
     *
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param SharedService $sharedService
     * @param QrPostingService $postingService
     * @throws InvalidArgumentException
     */
    private function _checkParams(HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, QrPostingService $postingService)
    {
        if ($headerValidators == null) {
            throw new InvalidArgumentException("HeaderValidatorCollection not found");
        }

        if ($rowValidators == null) {
            throw new InvalidArgumentException("HeaderValidatorCollection not found");
        }

        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($postingService == null) {
            throw new InvalidArgumentException("postingService service not found");
        }
    }
}