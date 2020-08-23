<?php
namespace Inventory\Application\Service\Upload\Transaction;

use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Service\AbstractService;
use Inventory\Application\Command\Transaction\Options\TrxRowCreateOptions;
use Inventory\Application\Service\Item\FIFOServiceImpl;
use Inventory\Application\Specification\Inventory\InventorySpecificationFactoryImpl;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\TrxPostingService;
use Inventory\Domain\Service\TrxValuationService;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxRowSnapshot;
use Inventory\Infrastructure\Doctrine\TrxCmdRepositoryImpl;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Procure\Application\Service\FXService;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxRowsUpload extends AbstractService
{

    /**
     *
     * @param GenericTrx $trx
     * @param string $file
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @return \Inventory\Domain\Transaction\GenericTrx
     */
    public function doUploading(GenericTrx $trx, $file)
    {
        if (! $trx instanceof GenericTrx) {
            throw new \InvalidArgumentException("GenericTrx not found!");
        }

        // take long time
        set_time_limit(2500);

        $sharedSpecsFactory = new ZendSpecificationFactory($this->getDoctrineEM());

        $fxService = new FXService();
        $fxService->setDoctrineEM($this->getDoctrineEM());

        $cmdRepository = new TrxCmdRepositoryImpl($this->getDoctrineEM());
        $postingService = new TrxPostingService($cmdRepository);

        $fifoService = new FIFOServiceImpl();
        $fifoService->setDoctrineEM($this->getDoctrineEM());
        $valuationService = new TrxValuationService($fifoService);

        $cmdRepository = new TrxCmdRepositoryImpl($this->getDoctrineEM());
        $postingService = new TrxPostingService($cmdRepository);

        // create share service.
        $sharedService = new SharedService($sharedSpecsFactory, $fxService, $postingService);
        $sharedService->setValuationService($valuationService);
        $sharedService->setDomainSpecificationFactory(new InventorySpecificationFactoryImpl($this->getDoctrineEM()));
        $sharedService->setLogger($this->getLogger());
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
                            case 3: // Doc Unit Price

                                $rowSnapshot->docUnitPrice = $val;
                                break;
                            case 4: // Net Amount

                                $rowSnapshot->netAmount = $val;
                                break;

                            case 5: // Convert Factor
                                $rowSnapshot->conversionFactor = $val;
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
    