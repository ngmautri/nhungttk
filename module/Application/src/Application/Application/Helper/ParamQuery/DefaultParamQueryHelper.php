<?php
namespace Application\Application\Helper\ParamQuery;

use Application\Application\Helper\ParamQueryColModeHelper;
use Application\Application\Helper\ParamQueryDataModelHelper;
use Application\Application\Helper\ParamQueryHelper;
use Application\Application\Helper\ParamQuery\Contracts\AbstractParamQueryColModel;
use Application\Application\Helper\ParamQuery\Contracts\AbstractParamQueryDataModel;

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
        $this->addOption($this->setHeight(500));
        $this->addOption($this->setWidth('100%'));
        $this->addOption($this->setShowTop(true));
        $this->addOption($this->setCollapsible(true));
        $this->addOption($this->setShowHeader(true));
        $this->addOption($this->setShowBottom(true));
        $this->addOption($this->setEditable(true));
        $this->addOption($this->setWrap(true));

        // Col Model
        $colModel = new ParamQueryColModeHelper();

        $col = new DefaultParamQueryColHelper();
        $col->addOption($col->setDataIndx("OK"));
        $col->addOption($col->setDataType(AbstractParamQueryColModel::INTEGER));
        $col->addOption($col->setTitle("Edit"));
        $render = 'function(ui) {
            return \'<button type="button" class="edit_btn">Edit</button > \';
        }';
        $col->addOption($col->setRender($render));
        $colModel->addColumn($col->getOutPut());

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
        $col->addOption($col->setMinWidth(55));
        $colModel->addColumn($col->getOutPut());

        $this->addComment("COLS MODEL");
        $this->addOption($this->setColModel($colModel->getOutPut()));

        // Data Model
        $dataModel = new ParamQueryDataModelHelper();
        $dataModel->addOption($dataModel->setLocation(AbstractParamQueryDataModel::LOCAL));
        $dataModel->addOption($dataModel->setUrl("sdsddsfgfd"));

        $this->addComment("DATA MODEL");
        $this->addOption($this->setDataModel($dataModel->getOutPut()));
    }
}
