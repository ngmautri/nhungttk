<?php
namespace Procure\Application\Service\Upload;

use Application\Domain\Company\CompanyVO;
use Doctrine\ORM\EntityManager;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Procure\Application\Command\Options\CreateRowCmdOptions;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Application\Service\Upload\Contracts\AbstractProcureRowsUpload;
use Procure\Domain\GenericDoc;
use Procure\Domain\PurchaseOrder\GenericPO;
use Procure\Domain\PurchaseOrder\PORowSnapshot;
use Procure\Domain\PurchaseOrder\PORowSnapshotAssembler;
use Procure\Domain\Service\SharedService;
use Procure\Application\Service\PO\RowSnapshotModifier;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UploadPoRows extends AbstractProcureRowsUpload
{

    protected $doctrineEM;

    public function __construct(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }

    protected function setUpSharedService()
    {
        return SharedServiceFactory::createForPO($this->doctrineEM);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Upload\Contracts\AbstractProcureRowsUpload::run()
     */
    protected function run(CompanyVO $companyVO, GenericDoc $doc, $file, SharedService $sharedService)
    {
        if (! $doc instanceof GenericPO) {
            throw new \InvalidArgumentException("Generic PO expected!");
        }

        $objPHPExcel = IOFactory::load($file);
        // $options = new CreateRowCmdOptions($doc, $doc->getId(), $doc->getToken(), $doc->getRevisionNo(), $doc->getCreatedBy(), __METHOD__);
        $options = new CreateRowCmdOptions($companyVO, $doc, $doc->getId(), $doc->getToken(), $doc->getRevisionNo(), $doc->getCreatedBy(), __METHOD__);

        $m = \sprintf("Uploading for PO #%s starts! Source [%s]  by [%s]", $doc->getId(), $file, $options->getUserId());
        $this->logInfo($m);

        try {

            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                // echo $worksheet->getTitle();

                // $worksheetTitle = $worksheet->getTitle();

                $highestRow = $worksheet->getHighestRow(); // e.g. 10
                $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
                // $nrColumns = ord($highestColumn) - 64;

                // echo $worksheetTitle;
                // echo $highestRow . "\n";
                // echo $highestColumnIndex;

                $n = 1;
                $header = 2;
                for ($row = $header + 1; $row <= $highestRow; ++ $row) {

                    $rowSnapshot = new PORowSnapshot();
                    $n ++;
                    // new A=1
                    for ($col = 1; $col < $highestColumnIndex + 1; ++ $col) {

                        $cell = $worksheet->getCellByColumnAndRow($col, $row);
                        $val = $cell->getValue();

                        switch ($col) {

                            case 1: // row number

                                $rowSnapshot->rowNumber = $val;
                                break;

                            case 2: // pr row

                                $rowSnapshot->prRow = $val;
                                break;

                            case 3:
                                // item id
                                $rowSnapshot->item = $val;
                                break;

                            case 4: // vendor item name

                                $rowSnapshot->vendorItemName = $val;
                                break;

                            case 5: // doc qty

                                $rowSnapshot->docQuantity = $val;
                                break;

                            case 6: // convert factor
                                $rowSnapshot->standardConvertFactor = $val;
                                break;

                            case 7: // unit
                                $rowSnapshot->docUnit = $val;
                                break;

                            case 8: // unit
                                $rowSnapshot->docUnitPrice = $val;
                                $rowSnapshot->exwUnitPrice = $val;

                                break;

                            case 9: // unit
                                break;

                            case 10: // remarks
                                $rowSnapshot->remarks = $val;
                                break;
                        }
                    }

                    $rowSnapshot = RowSnapshotModifier::modify($rowSnapshot, $this->getDoctrineEM());
                    $doc->createRowFrom($rowSnapshot, $options, $sharedService, false);
                }

                $doc->store($sharedService);

                $totalDoc = count($doc->getDocRows());
                $m = \sprintf("%s Rows of PO #%s uploaded and stored sucessfully! End.", $totalDoc, $doc->getId());
                $this->logInfo($m);

                return $doc;
            }
        } catch (\Exception $e) {
            \var_dump($rowSnapshot);
            var_dump($e->getMessage());
            $this->logException($e, false);
            throw new \RuntimeException($e->getMessage());
        }
    }
}
