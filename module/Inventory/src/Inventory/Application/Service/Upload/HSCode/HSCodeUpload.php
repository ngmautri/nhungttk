<?php
namespace Inventory\Application\Service\Upload\HSCode;

use Application\Entity\InventoryHsCode;
use Application\Service\AbstractService;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class HSCodeUpload extends AbstractService
{

    public function doUploading($file)
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
                // echo $highestRow;
                // echo $highestColumn;

                $n = 1;
                for ($row = 3; $row <= $highestRow; ++ $row) {

                    $entity = new InventoryHsCode();

                    $n ++;

                    // new A=1
                    for ($col = 1; $col < $highestColumnIndex; ++ $col) {

                        $cell = $worksheet->getCellByColumnAndRow($col, $row);
                        $val = $cell->getValue();

                        switch ($col) {
                            case 1: 
                                $entity->setId($val);
                                break;
                            case 2:
                                $entity->setParentId($val);
                                break;
                            case 3:
                                $entity->setCodeOrder($val);
                                break;
                            case 4:
                                $entity->setLevel($val);
                                break;
                            case 5:
                                $entity->setHsCode($val);
                                break;
                            case 6:
                                $entity->setParentCode($val);
                                break;
                            case 7:
                                $entity->setHsCode1($val);
                                break;
                            case 8:
                                $entity->setParentCode1($val);
                                break;
                            case 9:
                                $entity->setCodeDescription($val);
                                break;
                            case 10:
                                $entity->setCodeDescription1($val);
                                break;
                            case 11:
                                $entity->setReference($val);
                                break;
                            case 12:
                                $entity->setPath($val);
                                break;
                            case 13:
                                $entity->setPathDepth($val);
                                break;
                        }
                    }

                    $this->getDoctrineEM()->persist($entity);

                    if ($row % 100 == 0 or $row == $highestRow) {
                        $this->getDoctrineEM()->flush();
                    }
                }
            }

            $this->getLogger()->info(\sprintf("imported %s", $n));
        } catch (\Exception $e) {
            $this->getLogger()->error($e->getMessage());
        }
    }
}
    