<?php
namespace Application\Application\Helper;

use Application\Application\Helper\Contracts\AbstractParamQueryColModel;

/**
 * to create paramquerey gird
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ParamQueryColHelper extends AbstractParamQueryColModel
{

    protected $output;

    public function addOption($option)
    {
        $tmp = $this->output;

        if ($tmp == null) {
            $tmp = $option;
        } else {
            $tmp = $tmp . ',' . "\n" . $option;
        }

        $this->output = $tmp;
    }

    public function openTag()
    {
        return "{";
    }

    public function endTag()
    {
        return "}";
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
