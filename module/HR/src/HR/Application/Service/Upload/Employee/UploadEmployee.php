<?php
namespace HR\Application\Service\Upload\Employee;

use Application\Application\Command\Doctrine\GenericCommand;
use Application\Infrastructure\Doctrine\CompanyQueryRepositoryImpl;
use HR\Application\Command\TransactionalCommandHandler;
use HR\Application\Command\Doctrine\Employee\CreateIndividualFromDTOCmdHandler;
use HR\Application\Command\Options\CreateIndividualCmdOptions;
use HR\Application\DTO\Employee\IndividualDTO;
use HR\Application\Service\Upload\Employee\Contracts\AbstractEmployeeUpload;
use HR\Domain\Contracts\EmployeeStatus;
use HR\Domain\Contracts\IndividualType;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UploadEmployee extends AbstractEmployeeUpload
{

    /**
     *
     * {@inheritdoc}
     * @see \HR\Application\Service\Upload\Employee\Contracts\AbstractEmployeeUpload::run()
     */
    public function run($file)
    {
        $objPHPExcel = IOFactory::load($file);

        $rep = new CompanyQueryRepositoryImpl($this->doctrineEM);
        $companyVO = ($rep->getById($this->getCompanyId())
            ->createValueObject());

        try {

            // take long time
            set_time_limit(3500);

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

                $n = 3;
                for ($row = $n; $row <= $highestRow; ++ $row) {

                    $snapshot = new IndividualDTO();
                    $snapshot->individualType = IndividualType::EMPLOYEE;
                    $snapshot->employeeStatus = EmployeeStatus::EMPLOYED;
                    $snapshot->company = $this->getCompanyId();

                    $n ++;
                    // new A=1
                    for ($col = 1; $col < $highestColumnIndex + 1; ++ $col) {

                        $cell = $worksheet->getCellByColumnAndRow($col, $row);
                        $val = $cell->getValue();

                        switch ($col) {

                            case 2:
                                $snapshot->employeeCode = $val;
                                break;
                            case 3:
                                $snapshot->firstName = $val;
                                break;
                            case 4:
                                $snapshot->middleName = $val;
                                break;
                            case 5:
                                $snapshot->lastName = $val;
                                break;
                            case 6:
                                $snapshot->individualNameLocal = $val;
                                break;
                            case 7:
                                $snapshot->birthday = $val;
                                break;
                            case 8:
                                $snapshot->gender = $val;
                                break;
                            case 9:
                                $snapshot->familyBookNo = $val;
                                break;
                            case 10:
                                break;
                            case 11:

                                break;
                            case 12:
                                $snapshot->passportNo = $val;
                                break;
                            case 13:
                                $snapshot->passportIssuePlace = $val;
                                break;
                            case 14:
                                $snapshot->passportIssueDate = $val;
                                break;
                            case 15:
                                break;
                        }
                    }

                    $options = new CreateIndividualCmdOptions($companyVO, $this->getUserId(), __METHOD__);
                    $cmdHandler = new CreateIndividualFromDTOCmdHandler();
                    $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);
                    $cmd = new GenericCommand($this->getDoctrineEM(), $snapshot, $options, $cmdHandlerDecorator, $this->getEventBusService());
                    $cmd->execute();
                }
            }
        } catch (\Exception $e) {
            $this->logException($e, false);
            throw new \RuntimeException("Upload failed. The file might be not wrong.");
        }
    }
}
