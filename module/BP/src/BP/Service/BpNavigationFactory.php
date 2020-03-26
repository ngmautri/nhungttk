<?php
namespace BP\Service;

use Zend\Navigation\Service\AbstractNavigationFactory;

/**
 *
 * @author nmt
 *        
 */
class BpNavigationFactory extends AbstractNavigationFactory
{

    /**
     * Returns config name of the navigation
     *
     * @return string
     */
    public function getName()
    {
        return "bp_navi";
    }
}