<?php
namespace Application\Domain\Util\Collection\Render;

use Ramsey\Uuid\Uuid;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractRenderAsParamQuery extends AbstractCollectionRender
{

    private $totalResults;

    private $page;

    private $resultsPerPage;

    private $girdDiv;

    private $remoteUrl;

    private $editUrl;

    protected abstract function createParamQueryObject();

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Collection\Contracts\CollectionRenderInterface::execute()
     */
    public function execute()
    {
        // create gird holder.
        $this->setGirdDiv(Uuid::uuid4());

        // create gird div.
        $format = '<div id="%s"></div>';
        $result = sprintf($format, $this->getGirdDiv());

        $format = '<script>';
        $result = $result . $format;

        $result = $result . $this->createParamQueryObject();

        $format = "\n\n// Create ParamQuery Object!\n";
        $format = $format . 'var $grid = $("#%s").pqGrid(obj)';
        $result = $result . sprintf($format, $this->getGirdDiv());

        $format = '</script>';
        $result = $result . $format;

        return $result;
    }

    /*
     * |=============================
     * | SETTER
     * | GETTER
     * |=============================
     */
    /**
     *
     * @return mixed
     */
    public function getGirdDiv()
    {
        return $this->girdDiv;
    }

    /**
     *
     * @param mixed $girdDiv
     */
    public function setGirdDiv($girdDiv)
    {
        $this->girdDiv = $girdDiv;
    }
}

