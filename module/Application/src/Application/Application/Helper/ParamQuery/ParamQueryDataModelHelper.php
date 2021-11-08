<?php
namespace Application\Application\Helper\ParamQuery;

use Application\Application\Helper\ParamQuery\Contracts\AbstractParamQueryDataModel;

/**
 * to create paramquerey gird
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ParamQueryDataModelHelper extends AbstractParamQueryDataModel
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
        return " {\n";
    }

    public function endTag()
    {
        return "\n}";
    }

    public function getOutPut()
    {
        return $this->openTag() . $this->output . $this->endTag();
    }

    public function getParamQueryTemplate()
    {
        $t = '
        obj.dataModel = {
                    data: data,
                    location: "local",
                    sorting: "local",
                    sortDir: "down"
            };
        ';
        return $t;
    }
}
