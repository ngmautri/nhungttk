<?php
namespace Application\Domain\Util\Collection\Render;

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

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Collection\Contracts\CollectionRenderInterface::execute()
     */
    public function execute()
    {
        // do nothing
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

    /**
     *
     * @return mixed
     */
    public function getRemoteUrl()
    {
        return $this->remoteUrl;
    }

    /**
     *
     * @return mixed
     */
    public function getEditUrl()
    {
        return $this->editUrl;
    }

    /**
     *
     * @param mixed $remoteUrl
     */
    public function setRemoteUrl($remoteUrl)
    {
        $this->remoteUrl = $remoteUrl;
    }

    /**
     *
     * @param mixed $editUrl
     */
    public function setEditUrl($editUrl)
    {
        $this->editUrl = $editUrl;
    }
}

