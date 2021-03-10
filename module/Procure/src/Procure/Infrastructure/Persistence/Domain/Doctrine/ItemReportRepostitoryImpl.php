<?php
namespace Procure\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Procure\Infrastructure\Persistence\ItemReportRepositoryInterface;
use Procure\Infrastructure\Persistence\SQL\ItemReportSQL;
use Procure\Application\DTO\Item\ItemPriceCompareDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemReportRepostitoryImpl extends AbstractDoctrineRepository implements ItemReportRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\ItemReportRepositoryInterface::getPriceOfItem()
     */
    public function getPriceOfItem($id, $token = null, $sort_by = null, $sort = "ASC", $limit = 0, $offset = 0)
    {
        $sql = sprintf(ItemReportSQL::ITEM_PRICE_COMPARE_SQL, $id, $id, $id);

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }

        try {
            $stmt = $this->getDoctrineEM()
                ->getConnection()
                ->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();
            if (count($result) == 0) {
                return null;
            }

            $dtoArray = array();
            foreach ($result as $r) {
                $dtoArray[] = $this->makeDTO($r);
            }
            return $dtoArray;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @param
     *            $result
     */
    private function makeDTO($result)
    {
        if ($result == null) {
            return null;
        }

        $dto = new ItemPriceCompareDTO();
        $dto->docType = $result["doc_type"];
        $dto->rowIdentifer = $result["row_identifer"];
        $dto->itemId = $result["item_id"];
        $dto->itemName = $result["item_name"];
        $dto->itemSKU = $result["item_sku"];
        $dto->itemSKU1 = $result["item_sku1"];
        $dto->itemSKU2 = $result["item_sku2"];
        $dto->sourceId = $result["source_id"];
        $dto->vendorName = $result["vendor_name"];
        $dto->currencyId = $result["currency_id"];
        $dto->currencyISO = $result["currency"];
        $dto->docCurrencyId = $result["doc_currency_id"];
        $dto->docCurrencyISO = $result["doc_currency"];
        $dto->localCurrencyId = $result["local_currency_id"];
        $dto->localCurrencyISO = $result["local_currency"];
        $dto->exchangeRate = $result["exchange_rate"];
        $dto->sourceIsActive = $result["source_is_active"];
        $dto->sourceCreatedOn = $result["source_created_on"];
        $dto->docQuantity = $result["quantity"];
        $dto->convertFactor = $result["conversion_factor"];
        $dto->docUnit = $result["doc_unit"];
        $dto->docUnitPrice = $result["unit_price"];
        $dto->localUnitPrice = $result["lc_unit_price"];
        $dto->localTotalPrice = $result["lc_total_price"];
        $dto->incoterm = $result["incoterm"];
        $dto->incotermPlace = $result["incoterm_place"];
        $dto->vendorItemCode = $result["vendor_item_code"];
        $dto->vendorItemName = $result["vendor_item_name"];
        
        return $dto;
    }

}
