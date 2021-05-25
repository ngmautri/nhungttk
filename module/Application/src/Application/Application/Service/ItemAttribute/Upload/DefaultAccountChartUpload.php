<?php
namespace Application\Application\Service\AccountChart\Upload;

use Application\Application\Command\Options\CreateMemberCmdOptions;
use Application\Domain\Company\CompanyVO;
use Application\Domain\Company\AccountChart\BaseAccountSnapshot;
use Application\Domain\Company\AccountChart\BaseChart;
use Application\Domain\Service\SharedService;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class DefaultAccountChartUpload extends AbstractAccountChartUpload
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Service\AccountChart\Upload\AccountChartUploadInterface::run()
     */
    public function run(CompanyVO $companyVO, BaseChart $rootEntity, $file, CreateMemberCmdOptions $options, SharedService $sharedService)
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
                for ($row = 3; $row <= $highestRow; ++ $row) {

                    $snapshot = new BaseAccountSnapshot();
                    $n ++;
                    // new A=1
                    for ($col = 1; $col < $highestColumnIndex + 1; ++ $col) {

                        $cell = $worksheet->getCellByColumnAndRow($col, $row);
                        $val = $cell->getValue();

                        switch ($col) {
                            case 1:
                                // item id
                                $snapshot->accountNumber = $val;
                                break;
                            case 2: // Doc Quantity

                                $snapshot->accountName = $val;
                                break;
                            case 3: // Doc Unit Price

                                $snapshot->accountName1 = $val;
                                break;
                            case 4: // Net Amount

                                $snapshot->parentAccountNumber = $val;
                                break;
                        }
                    }

                    $rootEntity->createAccountFrom($snapshot, $options, $sharedService);
                }

                $this->logInfo($rootEntity->getAccountCollection()
                    ->count() . ' account will be stored.');
                $rootEntity->store($sharedService);

                return $rootEntity;
            }
        } catch (\Exception $e) {
            $this->logAlert($e->getMessage());
            $this->logException($e);
            throw new \RuntimeException("Upload failed. The file might be not wrong.");
        }
    }
}
