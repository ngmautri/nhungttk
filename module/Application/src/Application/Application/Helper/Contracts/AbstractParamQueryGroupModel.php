<?php
namespace Application\Application\Helper\Contracts;

/**
 * Abstract ParamQuery
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractParamQueryGroupModel
{

    protected $collapsed;

    protected $dataIndx;

    protected $dir;

    protected $icon;

    protected $summaryCls;

    protected $title;

    protected $titleCls;

    /**
     *
     * @return mixed
     */
    public function getCollapsed()
    {
        return $this->collapsed;
    }

    /**
     *
     * @param mixed $collapsed
     */
    public function setCollapsed($collapsed)
    {
        $this->collapsed = $collapsed;
    }

    /**
     *
     * @return mixed
     */
    public function getDataIndx()
    {
        return $this->dataIndx;
    }

    /**
     *
     * @param mixed $dataIndx
     */
    public function setDataIndx($dataIndx)
    {
        $this->dataIndx = $dataIndx;
    }

    /**
     *
     * @return mixed
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     *
     * @param mixed $dir
     */
    public function setDir($dir)
    {
        $this->dir = $dir;
    }

    /**
     *
     * @return mixed
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     *
     * @param mixed $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     *
     * @return mixed
     */
    public function getSummaryCls()
    {
        return $this->summaryCls;
    }

    /**
     *
     * @param mixed $summaryCls
     */
    public function setSummaryCls($summaryCls)
    {
        $this->summaryCls = $summaryCls;
    }

    /**
     *
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @return mixed
     */
    public function getTitleCls()
    {
        return $this->titleCls;
    }

    /**
     *
     * @param mixed $titleCls
     */
    public function setTitleCls($titleCls)
    {
        $this->titleCls = $titleCls;
    }
}
