<?php
namespace Procure\Application\Reporting\PO;

use Application\Service\AbstractService;
use Procure\Infrastructure\Persistence\Doctrine\POListRepositoryImpl;
use Procure\Application\Reporting\PO\Output\PoRowStatusOutputStrategy;
use Procure\Application\Reporting\PO\Output\PoRowStatusInArray;
use Procure\Application\Reporting\PO\Output\PoRowStatusInExcel;
use Procure\Application\Reporting\PO\Output\PoRowStatusInOpenOffice;

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

        $factory = null;
        switch ($outputStrategy) {
            case PoRowStatusOutputStrategy::OUTPUT_IN_ARRAY:
                $factory = new PoRowStatusInArray();
                break;
            case PoRowStatusOutputStrategy::OUTPUT_IN_EXCEL:
                $factory = new PoRowStatusInExcel();
                break;

            case PoRowStatusOutputStrategy::OUTPUT_IN_OPEN_OFFICE:
                $factory = new PoRowStatusInOpenOffice();
                break;
            default:
                $factory = new PoRowStatusInArray();
                break;
        }

        return $factory->createOutput($results);
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
