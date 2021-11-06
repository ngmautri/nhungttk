<?php
namespace Application\Domain\Util\Collection\Render;

use Application\Application\Service\Document\Pdf\AbstractBuilder;
use Application\Domain\Util\Collection\Render\Helper\RenderAsPdfHelper;

/**
 * Director in Builder Pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractRenderAsPdf extends AbstractCollectionRender
{

    protected $builder;

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Collection\Contracts\CollectionRenderInterface::execute()
     */
    public function execute()
    {
        if ($this->getBuilder() == null) {
            return null;
        }

        $params = [];
        $this->getBuilder()->buildHeader($params);

        $html = RenderAsPdfHelper::DEFAULT_CSS;

        $header = $html . '<div class="docType">Purchase Request</div><br>';
        $header = $header . \sprintf('<span class="docDetail">No.         : %s - Date: %s</span><br>', ucfirst($doc->getDocNumber()), $doc->getSubmittedOn());
        $header = $header . \sprintf('<span class="docDetail">Ref.        : %s</span><br>', $doc->getSysNumber());
        $header = $header . \sprintf('<span class="docDetail">Created by  : %s</span><br>', $doc->getCreatedByName());

        $details = $html . '<table  class="table-fill">
        <tr class="text-left" style="color:black;">
        <th class="text-left" style="width: 30px;">#</th>
        <th class="text-left" style="width: 40%;">Item</th>
        <th class="text-left" style="width: 50px;">Unit</th>
        <th class="text-left">Requested</th>
        <th class="text-left">Reveived</th>
        <th class="text-left">Open</th>
          </tr>';

        $n = 0;

        foreach ($this->getCollection() as $r) {

            $n ++;

            $r = $this->getFormatter()->format($r);

            $details .= '</tr>';
        }

        $details .= '</table>';

        $params = [
            "doc" => $doc,
            "header" => $header,
            "details" => $details
        ];
        $this->getBuilder()->buildBody($params);

        // created footer and export
        $this->getBuilder()->buildFooter();
    }

    /**
     *
     * @return \Application\Application\Service\Document\Pdf\AbstractBuilder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     *
     * @param AbstractBuilder $builder
     */
    public function setBuilder(AbstractBuilder $builder)
    {
        $this->builder = $builder;
    }
}
