<?php
namespace Inventory\Application\Service\Upload\Transaction;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Inventory\Application\DTO\Warehouse\Transaction\TransactionRowDTO;
use Inventory\Domain\Warehouse\Transaction\Factory\TransactionFactory;
use Inventory\Domain\Warehouse\Transaction\TransactionType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OpenBalanceUpload extends AbstractUploadStrategy
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Application\Service\Upload\Transaction\AbstractUploadStrategy::doUploading()
     */
    public function doUploading($file)
    {
        $objPHPExcel = IOFactory::load($file);

        try {
            
            $trx = TransactionFactory::createTransaction(TransactionType::GR_FROM_OPENNING_BALANCE);
            

            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                // echo $worksheet->getTitle();

                // $worksheetTitle = $worksheet->getTitle();

                $highestRow = $worksheet->getHighestRow(); // e.g. 10
                $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
                $nrColumns = ord($highestColumn) - 64;

                // echo $worksheetTitle;
                // echo $highestRow;
                echo $highestColumn;

                $transactionRows = array();

                $n=1;
                for ($row = 2; $row <= $highestRow; ++ $row) {

                    $transactionRowDTO = new TransactionRowDTO();
                    $n++;
                    // new A=1
                    for ($col = 1; $col < $highestColumnIndex; ++ $col) {

                        $cell = $worksheet->getCellByColumnAndRow($col, $row);
                        $val = $cell->getValue();

                        // echo $val . ';';

                        switch ($col) {
                            case 1:
                                // item id
                                $transactionRowDTO->item = $val;
                                break;
                            case 2: // Quanlity

                                $transactionRowDTO->quantity = $val;
                                $transactionRowDTO->docQuantity = $val;
                                break;
                            case 3: // Unit Price

                                $transactionRowDTO->docUnitPrice = $val;
                                break;
                            case 4: // Net Amount

                                $transactionRowDTO->docUnit = $val;

                                break;
                            case 5: // Gross Amount
                                $transactionRowDTO->do = $val;
                                break;
                        }
                    }

                    $transactionRows[] = $transactionRowDTO;
                }

                var_dump($transactionRows);
                echo($n);
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
    