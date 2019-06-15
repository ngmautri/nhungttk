<?php
namespace Procure\Application\Reporting\PR;

use Application\Service\AbstractService;
use Zend\Escaper\Escaper;
use Procure\Application\DTO\Pr\PrRowStatusDTO;
use Application\Entity\NmtProcurePrRow;

/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrRowStatusReporter extends AbstractService
{

    /**
     *
     * @var DoctrinePRListRepository;
     */
    private $prListRespository;

    public function getPrRowStatus($is_active = 1, $pr_year, $balance = 1, $sort_by, $sort, $limit, $offset)
    {
        $list = $this->getPrListRespository()->getAllPrRow($is_active, $pr_year, $balance, $sort_by, $sort, $limit, $offset);
        $total_records = count($list);

        $result = array();

        if ($total_records > 0) {
            foreach ($list as $a) {

                /**@var \Application\Entity\NmtProcurePrRow $pr_row_entity ;*/
                $pr_row_entity = $a[0];

                if ($pr_row_entity->getPr() == null) {
                    continue;
                }

                $dto = $this->createDTOFrom($pr_row_entity);

                if ($dto == null) {
                    continue;
                }

                $dto->poQuantity = $a['po_qty'];
                $dto->postedPoQuantity = $a['posted_po_qty'];

                $dto->grQuantity = $a['gr_qty'];
                $dto->postedGrQuantity = $a['posted_gr_qty'];

                $dto->stockGrQuantity = $a['stock_gr_qty'];
                $dto->postedStockGrQuantity = $a['posted_stock_gr_qty'];

                $dto->apQuantity = $a['ap_qty'];
                $dto->postedApQuantity = $a['posted_ap_qty'];

                $dto->prAutoNumber = $pr_row_entity->getPr()->getPrAutoNumber();
                $dto->prNumber = $pr_row_entity->getPr()->getPrNumber();
                $dto->prName = $pr_row_entity->getPr()->getPrName();
                $dto->prSubmittedOn = $pr_row_entity->getPr()->getSubmittedOn();
                $dto->prYear = $a['pr_year'];
                $dto->itemName = $a['item_name'];

                $result[] = $dto;
            }
        }

        return $result;
    }

    /**
     *
     * @param NmtProcurePrRow $entity
     * @return NULL|\Procure\Application\DTO\Pr\PrRowStatusDTO
     */
    public function createDTOFrom(NmtProcurePrRow $entity = null)
    {
        if ($entity == null) {
            return null;
        }

        /**@var \Application\Entity\NmtProcurePrRow $entity ;*/

        $dto = new PrRowStatusDTO();

        $dto->id = $entity->getId();
        $dto->rowNumber = $entity->getRowNumber();
        $dto->rowIdentifer = $entity->getRowIdentifer();
        $dto->token = $entity->getToken();
        $dto->checksum = $entity->getChecksum();
        $dto->priority = $entity->getPriority();
        $dto->rowName = $entity->getRowName();
        $dto->rowDescription = $entity->getRowDescription();
        $dto->rowCode = $entity->getRowCode();
        $dto->rowUnit = $entity->getRowUnit();
        $dto->conversionFactor = $entity->getConversionFactor();
        $dto->conversionText = $entity->getConversionText();
        $dto->quantity = $entity->getQuantity();
        $dto->edt = $entity->getEdt();
        $dto->isDraft = $entity->getIsDraft();
        $dto->isActive = $entity->getIsActive();
        $dto->createdOn = $entity->getCreatedOn();
        $dto->remarks = $entity->getRemarks();
        $dto->lastChangeOn = $entity->getLastChangeOn();
        $dto->currentState = $entity->getCurrentState();
        $dto->faRemarks = $entity->getFaRemarks();
        $dto->revisionNo = $entity->getRevisionNo();
        $dto->docStatus = $entity->getDocStatus();
        $dto->workflowStatus = $entity->getWorkflowStatus();
        $dto->transactionStatus = $entity->getTransactionStatus();
        $dto->convertedStockQuantity = $entity->getConvertedStockQuantity();
        $dto->convertedStandardQuantiy = $entity->getConvertedStandardQuantiy();
        $dto->docQuantity = $entity->getDocQuantity();
        $dto->docUnit = $entity->getDocUnit();
        $dto->docType = $entity->getDocType();
        $dto->reversalBlocked = $entity->getReversalBlocked();

        if ($entity->getCreatedBy() !== null) {
            $dto->createdBy = $entity->getCreatedBy()->getId();
        }

        if ($entity->getPr() !== null) {
            $dto->pr = $entity->getPr()->getId();
        }

        if ($entity->getItem() !== null) {
            $dto->item = $entity->getItem()->getId();
        }

        if ($entity->getProject() !== null) {
            $dto->project = $entity->getProject()->getId();
        }

        if ($entity->getLastChangeBy() !== null) {
            $dto->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        if ($entity->getDocUom() !== null) {
            $dto->docUom = $entity->getDocUom()->getId();
        }

        if ($entity->getWarehouse() !== null) {
            $dto->warehouse = $entity->getWarehouse()->getId();
        }

        return $dto;
    }

    /**
     *
     * @return \Procure\Infrastructure\Persistence\DoctrinePRListRepository
     */
    public function getPrListRespository()
    {
        return $this->prListRespository;
    }

    /**
     *
     * @param \Procure\Infrastructure\Persistence\DoctrinePRListRepository $prListRespository
     */
    public function setPrListRespository($prListRespository)
    {
        $this->prListRespository = $prListRespository;
    }
}
