<?php
namespace Application\Application\Helper\ParamQuery;

use Application\Application\Helper\ParamQuery\Contracts\AbstractParamQuery;

/**
 * to create paramquerey gird
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ParamQueryColModeHelper extends AbstractParamQuery
{

    protected $output;

    public function addColumn($column)
    {
        $tmp = $this->output;

        if ($tmp == null) {
            $tmp = $column;
        } else {
            $tmp = $tmp . ',' . "\n" . $column;
        }

        $this->output = $tmp;
    }

    public function openTag()
    {
        return " [\n";
    }

    public function endTag()
    {
        return "\n]";
    }

    public function getOutPut()
    {
        return $this->openTag() . $this->output . $this->endTag();
    }

    public function getParamQueryTemplate()
    {
        $t = <<<EOD
        var colM = [
        { title: "ShipCountry", width: 100 },
        { title: "Customer Name", width: 100 }];
        EOD;
        return $t;
    }
}
