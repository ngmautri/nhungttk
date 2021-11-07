<?php
namespace Inventory\Application\Reporting\ItemSerial\CollectionRender;

use Application\Application\Helper\DefaultParamQueryColHelper;
use Application\Application\Helper\ParamQueryColModeHelper;
use Application\Application\Helper\ParamQueryDataModelHelper;
use Application\Application\Helper\ParamQueryHelper;
use Application\Application\Helper\Contracts\AbstractParamQuery;
use Application\Application\Helper\Contracts\AbstractParamQueryColModel;
use Application\Application\Helper\Contracts\AbstractParamQueryDataModel;

/**
 * to create paramquerey gird
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSerialParamQueryHelper extends ParamQueryHelper
{

    public function __construct($remoteUrl)
    {
        $this->addOption($this->setHeight(500));
        $this->addOption($this->setWidth('100%'));
        $this->addOption($this->setShowTop(true));
        $this->addOption($this->setCollapsible(true));
        $this->addOption($this->setShowHeader(true));
        $this->addOption($this->setShowBottom(true));
        $this->addOption($this->setEditable(true));
        $this->addOption($this->setWrap(false));
        $this->addOption($this->setPageModel(AbstractParamQuery::PAGE_MODEL_REMOTE, 50));

        // pageModel: { type: "remote", rPP: 200, strRpp: "{0}" },

        // Col Model
        $colModel = new ParamQueryColModeHelper();

        $col = new DefaultParamQueryColHelper();
        $col->addOption($col->setDataType(AbstractParamQueryColModel::STRING));
        $col->addOption($col->setTitle("Edit"));
        $render = 'function(ui) {return \'<button type="button" class="edit_btn">Edit</button > \';}';
        $col->addOption($col->setRender($render));
        $col->addOption($col->setEditable(false));
        $colModel->addColumn($col->getOutPut());

        $col = new DefaultParamQueryColHelper();
        $col->addOption($col->setDataIndx("invoiceSysNumber"));
        $col->addOption($col->setDataType(AbstractParamQueryColModel::STRING));
        $col->addOption($col->setTitle("Invoice Number"));
        $col->addOption($col->setMinWidth(90));
        $colModel->addColumn($col->getOutPut());

        $col = new DefaultParamQueryColHelper();
        $col->addOption($col->setDataIndx("vendorName"));
        $col->addOption($col->setDataType(AbstractParamQueryColModel::STRING));
        $col->addOption($col->setTitle("Vendor Name"));
        $col->addOption($col->setMinWidth(150));
        $colModel->addColumn($col->getOutPut());

        $this->addComment("COLS MODEL");
        $this->addOption($this->setColModel($colModel->getOutPut()));

        // Data Model
        $dataModel = new ParamQueryDataModelHelper();
        $dataModel->addOption($dataModel->setLocation(AbstractParamQueryDataModel::REMOTE));
        $dataModel->addOption($dataModel->setUrl($remoteUrl));

        $this->addComment("DATA MODEL");
        $this->addOption($this->setDataModel($dataModel->getOutPut()));
    }
}
