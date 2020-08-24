<?php
namespace Inventory\Application\Service\Upload\Transaction;

use Inventory\Application\Command\Transaction\Options\TrxRowCreateOptions;
use Inventory\Application\Service\Upload\Transaction\Contracts\AbstractTrxRowsUpload;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxRowSnapshot;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class UploadGiForCostCenter extends AbstractTrxRowsUpload
{

    /**
     *
     * @param GenericTrx $trx
     * @param string $file
     * @param SharedService $sharedService
     * @throws \RuntimeException
     * @return \Inventory\Domain\Transaction\GenericTrx
     */
    protected function run(GenericTrx $trx, $file, SharedService $sharedService)
    {
        $objPHPExcel = IOFactory::load($file);
        $options = new TrxRowCreateOptions($trx, $trx->getId(), $trx->getToken(), $trx->getRevisionNo(), $trx->getCreatedBy(), __METHOD__);

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

                    $rowSnapshot = new TrxRowSnapshot();
                    $n ++;
                    // new A=1
                    for ($col = 1; $col < $highestColumnIndex + 1; ++ $col) {

                        $cell = $worksheet->getCellByColumnAndRow($col, $row);
                        $val = $cell->getValue();

                        switch ($col) {
                            case 1:
                                // item id
                                $rowSnapshot->item = $val;
                                break;
                            case 2: // Doc Quantity

                                $rowSnapshot->quantity = $val;
                                $rowSnapshot->docQuantity = $val;
                                break;
                            case 3: // Net Amount

                                $rowSnapshot->conversionFactor = $val;
                                break;

                            case 4: // Cost Center

                                $rowSnapshot->costCenter = $val;
                                break;

                            case 5: // Remarks
                                $rowSnapshot->remarks = $val;
                                break;
                        }
                    }

                    $trx->createRowFrom($rowSnapshot, $options, $sharedService, false);
                }

                $this->logInfo(count($trx->getDocRows()) . ' will be stored.');
                $trx->store($sharedService);

                return $trx;
            }
        } catch (\Exception $e) {
            $this->logException($e);
            throw new \RuntimeException("Upload failed. The file might be not wrong.");
        }
    }
}
    