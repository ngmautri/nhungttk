<?php
namespace Application\Application\DTO\Company\TreeNode;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 * @var \Application\Entity\NmtApplicationCompany ;
 */
class TreeNodeForOptionDTO
{

    public $nodeShowName;

    public $nodetName;

    public $nodeCode;

    public $contextObject;

    /**
     *
     * @return mixed
     */
    public function getNodeShowName()
    {
        return $this->nodeShowName;
    }

    /**
     *
     * @param mixed $nodeShowName
     */
    public function setNodeShowName($nodeShowName)
    {
        $this->nodeShowName = $nodeShowName;
    }

    /**
     *
     * @return mixed
     */
    public function getNodetName()
    {
        return $this->nodetName;
    }

    /**
     *
     * @param mixed $nodetName
     */
    public function setNodetName($nodetName)
    {
        $this->nodetName = $nodetName;
    }

    /**
     *
     * @return mixed
     */
    public function getNodeCode()
    {
        return $this->nodeCode;
    }

    /**
     *
     * @param mixed $nodeCode
     */
    public function setNodeCode($nodeCode)
    {
        $this->nodeCode = $nodeCode;
    }

    /**
     *
     * @return mixed
     */
    public function getContextObject()
    {
        return $this->contextObject;
    }

    /**
     *
     * @param mixed $contextObject
     */
    public function setContextObject($contextObject)
    {
        $this->contextObject = $contextObject;
    }
}
