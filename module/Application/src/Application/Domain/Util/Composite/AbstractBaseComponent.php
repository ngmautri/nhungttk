<?php
namespace Application\Domain\Util\Composite;

use Application\Domain\Util\Composite\Output\AbstractFormatter;
use Application\Domain\Util\Composite\Output\JsTreeFormatter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractBaseComponent extends AbstractComponent
{

    abstract public function getNumberOfChildren();

    /**
     *
     * @param AbstractFormatter $formatter
     * @return string
     */
    public function display(AbstractFormatter $formatter = null)
    {
        // default formatter
        if ($formatter == null) {
            $formatter = new JsTreeFormatter();
        }

        $results = $formatter->format($this);
        return $results;
    }

    /**
     *
     * @param AbstractComponent $component
     */
    public function add(AbstractComponent $component)
    {}

    /**
     *
     * @param AbstractComponent $component
     */
    public function remove(AbstractComponent $component)
    {}

    /**
     *
     * @return bool
     */
    public function isComposite()
    {
        return false;
    }

    public function updateFromGenericComponent(GenericComponent $input)
    {
        if (! $input instanceof GenericComponent) {
            return;
        }

        $this->setId($input->getId());
        $this->setParenId($input->getParenId());
        $this->setComponentCode($input->getComponentCode());
        $this->setComponentCode1($input->getComponentCode1());
        $this->setComponentDescription($input->getComponentDescription());
        $this->setComponentDescription1($input->getComponentDescription1());
        $this->setComponentLabel($input->getComponentLabel());
        $this->setComponentLabel1($input->getComponentLabel1());
        $this->setComponentName($input->getComponentName());
        $this->setComponentName1($input->getComponentName1());
        $this->setParentCode($input->getParentCode());
        $this->setParentLabel($input->getParentLabel());
    }

    public function showPathTo()
    {
        return $this->_showPathTo($this->getParent());
    }

    private function _showPathTo(AbstractComponent $component = null)
    {
        if ($component == null) {
            return "";
        }

        if ($component->getParent() !== null) {
            return $this->_showPathTo($component->getParent()) . "<br>" . $component->getComponentCode() . "  " . $component->getComponentName();
        }
    }
}
