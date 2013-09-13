<?php

namespace User\Form;

use Zend\Form\Form;

/*
 * NMT
 */
class UserForm extends Form
{
	public function __construct($name = null)
	{
		// we want to ignore the name passed
		parent::__construct('user');

		$this->add(array(
				'name' => 'firstname',
				'type' => 'Text',
				'options' => array(
						'label' => 'First name:',
				),
		));

		$this->add(array(
				'name' => 'lastname',
				'type' => 'Text',
				'options' => array(
						'label' => 'Last name:',
				),
		));

		$this->add(array(
				'name' => 'email',
				'type' => 'Text',
				'options' => array(
						'label' => 'Email:',
				),
		));

		$this->add(array(
				'name' => 'submit',
				'type' => 'Submit',
				'attributes' => array(
						'value' => 'Go',
						'id' => 'submitbutton',
				),
		));
	}
}

