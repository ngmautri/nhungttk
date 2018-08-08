<?php

namespace Payment\Service;
use Zend\Navigation\Service\AbstractNavigationFactory;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PaymentNavigationFactory extends AbstractNavigationFactory
{
	/**
	 * Returns config name of the navigation
	 *
	 * @return string
	 */
	public function getName()
	{
		return "payment_navi";
	}
}