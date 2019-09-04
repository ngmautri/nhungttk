<?php
namespace Procure\Application\DTO\Pr\Output;



/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class PRRowOutputStrategy
{

    const OUTPUT_IN_ARRAY = "1";

    const OUTPUT_IN_EXCEL = "2";

    const OUTPUT_IN_HMTL_TABLE = "3";

    const OUTPUT_IN_OPEN_OFFICE = "4";

    protected $output;

    /**
     *
     * @return mixed
     */
    public function getOutput()
    {
        if ($this->output == null)
            $this->output = array();

        return $this->output;
    }

    /**
     *
     * @param mixed $output
     */
    protected function setOutput($output)
    {
        $this->output = $output;
    }

    abstract public function createOutput($result);

    abstract public function createRowOutputFromSnapshot($result);

    
    public function createDTOFrom( $entity = null)
    {
    }
}
