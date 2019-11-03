<?php
namespace Procure\Application\Reporting\PR;

use Application\Service\AbstractService;
use Procure\Application\Reporting\PR\Output\PrRowStatusInArray;
use Procure\Application\Reporting\PR\Output\PrRowStatusInExcel;
use Procure\Application\Reporting\PR\Output\PrRowStatusInHTMLTable;
use Procure\Application\Reporting\PR\Output\PrRowStatusInOpenOffice;
use Procure\Application\Reporting\PR\Output\PrRowStatusOutputStrategy;
use Procure\Infrastructure\Persistence\Doctrine\PRListRepository;

/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrRowStatusReporter extends AbstractService
{

   
    private $prListRespository;

    public function getPrRowStatus($is_active = 1, $pr_year, $balance = 1, $sort_by, $sort, $limit, $offset, $outputStrategy)
    {
        $factory = null;
        switch ($outputStrategy) {
            case \Procure\Application\Reporting\PR\Output\PrRowStatusOutputStrategy::OUTPUT_IN_ARRAY:
                $factory = new PrRowStatusInArray();
                break;
            case PrRowStatusOutputStrategy::OUTPUT_IN_EXCEL:
                $limit = 0;
                $offset = 0;

                // download all
                $factory = new PrRowStatusInExcel();
                break;

            case PrRowStatusOutputStrategy::OUTPUT_IN_OPEN_OFFICE:
                $limit = 0;
                $offset = 0;

                // download all
                $factory = new PrRowStatusInOpenOffice();
                break;

            case PrRowStatusOutputStrategy::OUTPUT_IN_HMTL_TABLE:
                $factory = new PrRowStatusInHTMLTable();
                $factory->setOffset($offset);
                break;

            default:
                $factory = new PrRowStatusInArray();
                break;
        }

        $result = $this->getPrListRespository()->getAllPrRow($is_active, $pr_year, $balance, $sort_by, $sort, $limit, $offset);
        
        
        return $factory->createOutput($result);
    }

    /**
     *
     * @param int $prId
     * @param int $balance
     * @param string $sort_by
     * @param string $sort
     * @param string $outputStrategy
     * @return NULL|NULL[]|\Procure\Application\DTO\Pr\PrRowStatusDTO[]
     */
    public function getPrStatus($prId = null, $balance = 1, $sort_by, $sort, $outputStrategy)
    {
        $factory = null;
        switch ($outputStrategy) {
            case \Procure\Application\Reporting\PR\Output\PrRowStatusOutputStrategy::OUTPUT_IN_ARRAY:
                $factory = new PrRowStatusInArray();
                break;
            case PrRowStatusOutputStrategy::OUTPUT_IN_EXCEL:
                // download all
                $factory = new PrRowStatusInExcel();
                break;

            case PrRowStatusOutputStrategy::OUTPUT_IN_OPEN_OFFICE:
                // download all
                $factory = new PrRowStatusInOpenOffice();
                break;

            case PrRowStatusOutputStrategy::OUTPUT_IN_HMTL_TABLE:
                $factory = new PrRowStatusInHTMLTable();
                break;

            default:
                $factory = new PrRowStatusInArray();
                break;
        }

        $result = $this->prListRespository->getPrStatus($prId, $balance);
        return $factory->createOutput($result);
    }

    public function getPrRowStatusTotal($is_active = 1, $pr_year, $balance = 1, $sort_by, $sort, $limit, $offset, $outputStrategy)
    {
        $result = $this->getPrListRespository()->getAllPrRowTotal($is_active, $pr_year, $balance, $sort_by, $sort, $limit, $offset);
        return $result;
    }
    
 
   /**
    * 
    * @return \Procure\Infrastructure\Persistence\Doctrine\PRListRepository
    */
    public function getPrListRespository()
    {
        return $this->prListRespository;
    }

 /**
  * 
  * @param PRListRepository $prListRespository
  */
    public function setPrListRespository(PRListRepository $prListRespository)
    {
        $this->prListRespository = $prListRespository;
    }


 
}
