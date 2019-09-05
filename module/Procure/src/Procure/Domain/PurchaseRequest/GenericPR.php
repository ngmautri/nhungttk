<?php
namespace Procure\Domain\PurchaseRequest;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GenericPR extends AbstractPR
{

    protected $docRows;

    protected $rowsOutput;
    
    

    /**
     *
     * @param mixed $docRows
     */
    public function setDocRows($docRows)
    {
        $this->docRows = $docRows;
    }
    /**
     * @return mixed
     */
    public function getRowsOutput()
    {
        return $this->rowsOutput;
    }

    /**
     * @param mixed $rowsOutput
     */
    public function setRowsOutput($rowsOutput)
    {
        $this->rowsOutput = $rowsOutput;
    }

    /**
     * @return mixed
     */
    public function getDocRows()
    {
        return $this->docRows;
    }

}