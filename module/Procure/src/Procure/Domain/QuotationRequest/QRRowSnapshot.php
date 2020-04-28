<?php
namespace Procure\Domain\QuotationRequest;

use Procure\Domain\RowSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class QRRowSnapshot extends RowSnapshot
{

    public $qo;

    /**
     *
     * @return mixed
     */
    public function getQo()
    {
        return $this->qo;
    }
}