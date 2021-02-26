<?php
namespace Procure\Application\Service\Upload\PR;

use Inventory\Application\Command\GenericCmd;
use Inventory\Application\Command\TransactionalCmdHandlerDecorator;
use Inventory\Application\Command\Item\CreateCmdHandler;
use Inventory\Application\Command\Item\Options\CreateItemOptions;
use Inventory\Application\Service\Upload\Item\Contracts\AbstractItemUpload;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Procure\Application\DTO\Pr\PrRowDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UploadPR extends AbstractItemUpload
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Application\Service\Upload\Item\Contracts\AbstractItemUpload::run()
     */
    public function run($file)
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

                    $snapshot = new PrRowDTO();
                    $n ++;
                    // new A=1
                    for ($col = 1; $col < $highestColumnIndex + 1; ++ $col) {

                        $cell = $worksheet->getCellByColumnAndRow($col, $row);
                        $val = $cell->getValue();

                        switch ($col) {

                            case 1: // row number

                                $snapshot->rowNumber = $val;
                                break;

                            case 2:
                                // item id
                                $snapshot->item = $val;
                                break;

                            case 3: // vendor item name

                                $snapshot->vendorItemName = $val;
                                break;

                            case 4: // doc qty

                                $snapshot->docQuantity = $val;
                                break;

                            case 5: // convert factor
                                $snapshot->standardConvertFactor = $val;
                                break;

                            case 6: // unit
                                $snapshot->docUnit = $val;
                                break;

                            case 7: // Edt
                                $snapshot->edt = $val;
                                break;

                            case 8: // remarks
                                $snapshot->manufacturerModel = $val;
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
