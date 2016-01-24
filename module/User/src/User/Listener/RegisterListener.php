<?php

namespace User\Listener;

use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

use Zend\Mail\Message;
use Zend\Mail\Transport\File;
use User\Model\User;

class RegisterListener implements ListenerAggregateInterface {
	/**
	 *
	 * @var array
	 */
	protected $listeners = array ();
	protected $mailTransport = null;
	public function attach(EventManagerInterface $events) {
		$this->listeners [] = $events->attach ( 'postRegister', array (
				$this,
				'sendConfirmation' 
		), 100 );
	}
	public function detach(EventManagerInterface $events) {
		foreach ( $this->listeners as $index => $listener ) {
			if ($events->detach ( $listener )) {
				unset ( $this->listeners [$index] );
			}
		}
	}
	public function setMailTransport($mailTransport) {
		$this->mailTransport = $mailTransport;
	}
	/**
	 *
	 * @return File
	 */
	public function getMailTransport() {
		return $this->mailTransport;
	}
	
	/**
	 * 
	 * @param EventInterface $e
	 */
	public function sendConfirmation(EventInterface $e) {
		/* @var $order \ArrayObject */
		$user = $e->getParam ('user');
		
		// build message
		$message = new Message ();
		$message->setEncoding ( 'utf-8' );
		$message->addFrom ( 'mib-team@web.de' );
		$message->addTo ($user->email);
		$message->setSubject ( 'Mascot Laos Plattform Register' );
		$message->setBody ( 'please confirm "' . $user->registration_key);
		// send message
		$this->getMailTransport ()->send ( $message );
	}
}