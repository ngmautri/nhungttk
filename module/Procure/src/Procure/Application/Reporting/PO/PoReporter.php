<?php
namespace Procure\Application\Reporting\PO;

use Application\Service\AbstractService;
use Procure\Infrastructure\Persistence\Doctrine\POListRepository;

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
     * @var POListRepository $prListRespository;
     */
    private $listRespository;

    public function getPoList($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0, $outputStrategy = null)
    {
        $results = $this->getListRespository()->getPoList($is_active, $current_state, $docStatus, $filter_by, $sort_by, $sort, $limit, $offset);
        return $results;
    }

    /**
     *
     * @return \Procure\Infrastructure\Persistence\Doctrine\POListRepository
     */
    public function getListRespository()
    {
        return $this->listRespository;
    }

    /**
     *
     * @param POListRepository $listRespository
     */
    public function setListRespository(POListRepository $listRespository)
    {
        $this->listRespository = $listRespository;
    }
}
