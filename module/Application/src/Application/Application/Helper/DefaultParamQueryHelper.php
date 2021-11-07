<?php
namespace Application\Application\Helper;

use Application\Application\Helper\Contracts\AbstractParamQueryColModel;
use Application\Application\Helper\Contracts\AbstractParamQueryDataModel;

/**
 * to create paramquerey gird
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultParamQueryHelper extends ParamQueryHelper
{

    public function __construct()
    {
        $this->addOption($this->setHeight(400));
        $this->addOption($this->setWidth(400));
        $this->addOption($this->setShowTop(true));
        $this->addOption($this->setCollapsible(true));

        $this->addOption($this->setShowHeader(true));
        $this->addOption($this->setShowBottom(true));
        $this->addOption($this->setEditable(true));
        $this->addOption($this->setWrap(true));

        $colModel = new ParamQueryColModeHelper();

        $col = new DefaultParamQueryColHelper();
        $col->addOption($col->setDataIndx("OK"));
        $col->addOption($col->setDataType(AbstractParamQueryColModel::INTEGER));
        $col->addOption($col->setTitle("OK"));
        $colModel->addColumn($col->getOutPut());

        $col = new DefaultParamQueryColHelper();

        $col->addOption($col->setDataIndx("OK"));
        $col->addOption($col->setDataType(AbstractParamQueryColModel::INTEGER));
        $col->addOption($col->setTitle("Test"));
        $col->addOption($col->setAlign(AbstractParamQueryColModel::LEFT));
        $colModel->addColumn($col->getOutPut());

        $dataModel = new ParamQueryDataModelHelper();
        $dataModel->addOption($dataModel->setLocation(AbstractParamQueryDataModel::LOCAL));
        $dataModel->addOption($dataModel->setUrl("sdsddsfgfd"));

        $this->addComment("DATA MODEL");
        $this->addOption($this->setDataModel($dataModel->getOutPut()));

        $this->addComment("COLS MODEL");
        $this->addOption($this->setColModel($colModel->getOutPut()));
    }
}
