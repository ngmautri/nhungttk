<?php
namespace Procure\Application\DTO\Pr;

use Procure\Domain\PurchaseRequest\PRSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrHeaderDetailDTO extends PRSnapshot
{

    public $grCompletedRows;

    public $apCompletedRows;

    public $grPartialCompletedRows;

    public $apPartialCompletedRows;

    /**
     *
     * @return mixed
     */
    public function getGrCompletedRows()
    {
        return $this->grCompletedRows;
    }

    /**
     *
     * @return mixed
     */
    public function getApCompletedRows()
    {
        return $this->apCompletedRows;
    }

    /**
     *
     * @return mixed
     */
    public function getGrPartialCompletedRows()
    {
        return $this->grPartialCompletedRows;
    }

    /**
     *
     * @return mixed
     */
    public function getApPartialCompletedRows()
    {
        return $this->apPartialCompletedRows;
    }
}
