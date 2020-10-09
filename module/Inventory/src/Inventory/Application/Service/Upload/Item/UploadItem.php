<?php
namespace Inventory\Application\Service\Upload\Item;

use Inventory\Application\Command\GenericCmd;
use Inventory\Application\Command\TransactionalCmdHandlerDecorator;
use Inventory\Application\Command\Item\CreateCmdHandler;
use Inventory\Application\Command\Item\Options\CreateItemOptions;
use Inventory\Application\DTO\Item\ItemDTO;
use Inventory\Application\Service\Upload\Item\Contracts\AbstractItemUpload;
use Inventory\Domain\Item\GenericItem;
use Inventory\Domain\Service\SharedService;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UploadItem extends AbstractItemUpload
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Application\Service\Upload\Item\Contracts\AbstractItemUpload::run()
     */
    public  function run($file)
    {
        $objPHPExcel = IOFactory::load($file);

        try {

            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                // echo $worksheet->getTitle();

                // $worksheetTitle = $worksheet->getTitle();

                $highestRow = $worksheet->getHighestRow(); // e.g. 10
                $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
                $nrColumns = ord($highestColumn) - 64;

                // echo $worksheetTitle;
                // echo $highestRow . "\n";
                // echo $highestColumnIndex;

                $n = 1;
                for ($row = 2; $row <= $highestRow; ++ $row) {

                    $snapshot = new ItemDTO();
                    $n ++;
                    // new A=1
                    for ($col = 1; $col < $highestColumnIndex + 1; ++ $col) {

                        $cell = $worksheet->getCellByColumnAndRow($col, $row);
                        $val = $cell->getValue();

                        switch ($col) {
                            case 1:
                                // item sku
                                $snapshot->itemSku = $val;
                                break;
                            case 2:
                                // item sku
                                $snapshot->itemSku1 = $val;
                                break;
                            case 3:
                                $snapshot->itemName = $val;
                                break;
                            case 4:
                                $snapshot->itemNameForeign = $val;
                                break;
                            case 5:
                                $snapshot->manufacturerCode = $val;
                                break;
                            case 6:
                                $snapshot->manufacturerModel = $val;
                                break;
                            case 7:
                                $snapshot->manufacturerSerial = $val;
                                break;
                            case 8:
                                $snapshot->itemTypeId = $val;
                                break;
                            case 9:
                                $snapshot->itemGroup = $val;
                                break;
                            case 10:
                                $snapshot->hsCode = $val;
                                break;
                            case 11:
                                $snapshot->standardUom = $val;
                                $snapshot->uom = $val;
                                break;
                            case 12:
                                $snapshot->stockUom = $val;
                                break;
                            case 13:
                                $snapshot->stockUomConvertFactor = $val;
                                break;
                            case 14:
                                $snapshot->purchaseUom = $val;
                                break;
                            case 15:
                                $snapshot->purchaseUomConvertFactora = $val;
                                break;
                        }
                    }

                    $options = new CreateItemOptions($this->getCompanyId(), $this->getUserId(), __METHOD__);
                    $cmdHandler = new CreateCmdHandler();
                    $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
                    $cmd = new GenericCmd($this->getDoctrineEM(), $snapshot, $options, $cmdHandlerDecorator, $this->getEventBusService());
                    $cmd->execute();
                }
            }
        } catch (\Exception $e) {
            $this->logException($e, false);
            throw new \RuntimeException("Upload failed. The file might be not wrong.");
        }
    }
}
