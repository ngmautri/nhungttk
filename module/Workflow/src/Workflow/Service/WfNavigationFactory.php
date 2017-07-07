<?php

namespace Workflow\Service;
use Zend\Navigation\Service\AbstractNavigationFactory;

/**
 *
 * @author nmt
 *        
 */
class WfNavigationFactory extends AbstractNavigationFactory
{
	/**
	 * Returns config name of the navigation
	 *
	 * @return string
	 */
	public function getName()
	{
		return "wf_navi";
	}
}