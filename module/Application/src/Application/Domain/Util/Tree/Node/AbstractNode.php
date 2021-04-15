<?php
namespace Application\Domain\Util\Tree\Node;

use SplObjectStorage;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractNode implements NodeInterface
{

    protected $id;

    protected $contextObject;

    protected $level;

    protected $nodeCode;

    protected $nodeCode1;

    protected $nodeLabel;

    protected $nodeLabel1;

    protected $nodeName;

    protected $nodeName1;

    protected $nodeDescription;

    protected $nodeDescription1;

    protected $parentCode;

    protected $parentLabel;

    protected $parentId;

    /**
     *
     * @var \SplObjectStorage
     */
    protected $children;

    /**
     *
     * @var AbstractBaseNode
     */
    protected $parent;

    protected $allowChildren;

    /**
     *
     * @return mixed
     */
    public function getAllowChildren()
    {
        return $this->allowChildren;
    }

    /**
     *
     * @param mixed $allowChildren
     */
    public function setAllowChildren($allowChildren)
    {
        $this->allowChildren = $allowChildren;
    }

    /**
     *
     * @return \Application\Domain\Util\Tree\Node\AbstractBaseNode
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     *
     * @param AbstractBaseNode $parent
     */
    public function setParent(AbstractBaseNode $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     *
     * @return SplObjectStorage
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     *
     * @param SplObjectStorage $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
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
     * @return mixed
     */
    public function getNodeCode1()
    {
        return $this->nodeCode1;
    }

    /**
     *
     * @return mixed
     */
    public function getNodeLabel()
    {
        return $this->nodeLabel;
    }

    /**
     *
     * @return mixed
     */
    public function getNodeLabel1()
    {
        return $this->nodeLabel1;
    }

    /**
     *
     * @return mixed
     */
    public function getNodeName()
    {
        return $this->nodeName;
    }

    /**
     *
     * @return mixed
     */
    public function getNodeName1()
    {
        return $this->nodeName1;
    }

    /**
     *
     * @return mixed
     */
    public function getNodeDescription()
    {
        return $this->nodeDescription;
    }

    /**
     *
     * @return mixed
     */
    public function getNodeDescription1()
    {
        return $this->nodeDescription1;
    }

    /**
     *
     * @return mixed
     */
    public function getParentCode()
    {
        return $this->parentCode;
    }

    /**
     *
     * @return mixed
     */
    public function getParentLabel()
    {
        return $this->parentLabel;
    }

    /**
     *
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     *
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param mixed $contextObject
     */
    public function setContextObject($contextObject)
    {
        $this->contextObject = $contextObject;
    }

    /**
     *
     * @param mixed $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
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
     * @param mixed $nodeCode1
     */
    public function setNodeCode1($nodeCode1)
    {
        $this->nodeCode1 = $nodeCode1;
    }

    /**
     *
     * @param mixed $nodeLabel
     */
    public function setNodeLabel($nodeLabel)
    {
        $this->nodeLabel = $nodeLabel;
    }

    /**
     *
     * @param mixed $nodeLabel1
     */
    public function setNodeLabel1($nodeLabel1)
    {
        $this->nodeLabel1 = $nodeLabel1;
    }

    /**
     *
     * @param mixed $nodeName
     */
    public function setNodeName($nodeName)
    {
        $this->nodeName = $nodeName;
    }

    /**
     *
     * @param mixed $nodeName1
     */
    public function setNodeName1($nodeName1)
    {
        $this->nodeName1 = $nodeName1;
    }

    /**
     *
     * @param mixed $nodeDescription
     */
    public function setNodeDescription($nodeDescription)
    {
        $this->nodeDescription = $nodeDescription;
    }

    /**
     *
     * @param mixed $nodeDescription1
     */
    public function setNodeDescription1($nodeDescription1)
    {
        $this->nodeDescription1 = $nodeDescription1;
    }

    /**
     *
     * @param mixed $parentCode
     */
    public function setParentCode($parentCode)
    {
        $this->parentCode = $parentCode;
    }

    /**
     *
     * @param mixed $parentLabel
     */
    public function setParentLabel($parentLabel)
    {
        $this->parentLabel = $parentLabel;
    }

    /**
     *
     * @param mixed $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }
}
