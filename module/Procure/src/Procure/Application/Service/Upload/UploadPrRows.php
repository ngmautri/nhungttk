<?php
namespace Procure\Application\Service\Upload;

use Application\Domain\Company\CompanyVO;
use Doctrine\ORM\EntityManager;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Procure\Application\Command\Options\CreateRowCmdOptions;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Application\Service\Upload\Contracts\AbstractProcureRowsUpload;
use Procure\Domain\GenericDoc;
use Procure\Domain\PurchaseRequest\GenericPR;
use Procure\Domain\PurchaseRequest\PRRowSnapshot;
use Procure\Domain\Service\SharedService;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UploadPrRows extends AbstractProcureRowsUpload
{

    protected $doctrineEM;

    public function __construct(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }

    protected function setUpSharedService()
    {
        return SharedServiceFactory::createForPR($this->doctrineEM);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Upload\Contracts\AbstractProcureRowsUpload::run()
     */
    protected function run(CompanyVO $companyVO, GenericDoc $doc, $file, SharedService $sharedService)
    {
        if (! $doc instanceof GenericPR) {
            throw new \InvalidArgumentException("Generic PR expected!");
        }

        $objPHPExcel = IOFactory::load($file);
        // $options = new CreateRowCmdOptions($doc, $doc->getId(), $doc->getToken(), $doc->getRevisionNo(), $doc->getCreatedBy(), __METHOD__);
        $options = new CreateRowCmdOptions($companyVO, $doc, $doc->getId(), $doc->getToken(), $doc->getToken(), $doc->getRevisionNo(), $doc->getCreatedBy(), __METHOD__);

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

                    $rowSnapshot = new PRRowSnapshot();
                    $n ++;
                    // new A=1
                    for ($col = 1; $col < $highestColumnIndex + 1; ++ $col) {

                        $cell = $worksheet->getCellByColumnAndRow($col, $row);
                        $val = $cell->getValue();

                        switch ($col) {

                            case 1: // row number

                                $rowSnapshot->rowNumber = $val;
                                break;

                            case 2:
                                // item id
                                $rowSnapshot->item = $val;
                                break;

                            case 3: // vendor item name

                                $rowSnapshot->vendorItemName = $val;
                                break;

                            case 4: // doc qty

                                $rowSnapshot->docQuantity = $val;
                                break;

                            case 5: // convert factor
                                $rowSnapshot->standardConvertFactor = $val;
                                break;

                            case 6: // unit
                                $rowSnapshot->docUnit = $val;
                                break;

                            case 7: // Edt
                                $rowSnapshot->edt = $val;
                                break;

                            case 8: // remarks
                                $rowSnapshot->remarks = $val;
                                break;
                        }
                    }
                    $doc->createRowFrom($rowSnapshot, $options, $sharedService, false);
                }
                $m = \sprintf("%s PR rows will be stored", count($doc->getDocRows()));
                $this->logInfo($m);
                $doc->store($sharedService);
                return $doc;
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            $this->logException($e, false);
            throw new \RuntimeException("Upload failed. The file might be not wrong.");
        }
    }
}
