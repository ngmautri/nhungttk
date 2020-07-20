<?php
namespace Application\Domain\Util\Composite\Output;

use Application\Domain\Util\Composite\AbstractBaseComponent;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SimpleFormatter extends AbstractFormatter
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Composite\Output\AbstractFormatter::format()
     */
    public function format(AbstractBaseComponent $component, $level = 0)

    {
        $results = '';
        // $results = $results . sprintf("<li data-jstree='{ \"opened\" : true}'>%s %s\n", "", $this->getComponentName(), $this->getNumberOfChildren());

        if ($component->isComposite()) {

            $format = '[+] %s %s (%s)' . "\n";
            $results = $results . sprintf($format, $component->getComponentCode(), $component->getComponentName(), $component->getNumberOfChildren() - 1);
            $results = $results . "<br>";

            foreach ($component->getChildren() as $child) {
                // recursive
                $space = '';
                for ($i = 0; $i <= $level; $i ++) {
                    $space = $space . "&nbsp;&nbsp&nbsp;&nbsp&nbsp;&nbsp&nbsp;&nbsp&nbsp;&nbsp&nbsp;&nbsp";
                }
                $results = $results . $space . $child->display($this, $level + 1);
            }
        } else {
            $space = '';
            for ($i = 0; $i <= $level; $i ++) {
                $space = $space . "&nbsp;&nbsp&nbsp;&nbsp&nbsp;&nbsp&nbsp;&nbsp&nbsp;&nbsp&nbsp;&nbsp";
            }
            $format = '- %s %s' . "\n";
            $results = $results . $space . sprintf($format, $component->getComponentCode(), $component->getComponentName());
            $results = $results . "<br>";
        }

        return $results;
    }
}
