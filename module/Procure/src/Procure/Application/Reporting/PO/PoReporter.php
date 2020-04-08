<?php
namespace Procure\Application\Reporting\PO;

use Application\Service\AbstractService;
use Procure\Application\Reporting\PO\Output\PoRowStatusInExcel;
use Procure\Application\Reporting\PO\Output\PoRowStatusInOpenOffice;
use Procure\Application\Service\Output\RowInArray;
use Procure\Application\Service\Output\RowOutputStrategy;
use Procure\Application\Service\PO\Output\PoRowInExcel;
use Procure\Infrastructure\Persistence\Doctrine\POListRepositoryImpl;
use Procure\Application\Service\PO\Output\PoRowInArray;
use Procure\Application\Service\Output\SaveAsSupportedType;
use Procure\Application\Service\PO\Output\PoRowFormatter;
use Procure\Application\Service\Output\RowNumberFormatter;
use Procure\Application\Service\PO\Output\PoSaveAsExcel;
use Procure\Application\Service\Output\SaveAsArray;
use Procure\Application\Service\Output\RowFormatter;
use Procure\Application\Service\PO\Output\PoSaveAsOpenOffice;

/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoReporter extends AbstractService
{

    /**
     *
     * @var POListRepositoryImpl $prListRespository;
     */
    private $listRespository;

    public function getPoList($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0, $outputStrategy = null)
    {
        $results = $this->getListRespository()->getPoList($is_active, $current_state, $docStatus, $filter_by, $sort_by, $sort, $limit, $offset);
        return $results;
    }

    public function getAllPoRowStatus($is_active = 1, $po_year, $balance = 1, $sort_by, $sort, $limit, $offset, $outputStrategy)
    {
        
        $results = $this->getListRespository()->getAllPoRowStatus($is_active, $po_year, $balance, $sort_by, $sort, $limit, $offset);
        
          
        if ($results == null) {
            return null;
        }

        //var_dump($results);
        
        $factory = null;
        $formatter = null;
        
        switch ($outputStrategy) {
            case RowOutputStrategy::OUTPUT_IN_ARRAY:
                $formatter = new PoRowFormatter(new RowFormatter());
                $factory = new SaveAsArray();
                break;
            case SaveAsSupportedType::OUTPUT_IN_EXCEL:
                $formatter = new PoRowFormatter(new RowNumberFormatter());
                $factory = new PoSaveAsExcel();
                break;
            
            case SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE:
                $formatter = new PoRowFormatter(new RowNumberFormatter());
                $factory = new PoSaveAsOpenOffice();                
                 break;
            default:
                $formatter = new PoRowFormatter(new RowFormatter());
                $factory = new SaveAsArray();
                break;
        }

        return $factory->saveMultiplyRowsAs($results,$formatter);
    }

    
    public function getAllPoRowStatusTotal($is_active = 1, $po_year, $balance = 1)
    {
        return $this->getListRespository()->getAllPoRowStatusTotal($is_active, $po_year, $balance);
    }
    
    /**
     *
     * @return \Procure\Infrastructure\Persistence\Doctrine\POListRepositoryImpl
     */
    public function getListRespository()
    {
        return $this->listRespository;
    }

    /**
     *
     * @param POListRepositoryImpl $listRespository
     */
    public function setListRespository(POListRepositoryImpl $listRespository)
    {
        $this->listRespository = $listRespository;
    }
}
