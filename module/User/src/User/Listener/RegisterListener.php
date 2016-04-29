<?php

namespace User\Listener;

use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

use Zend\Mail\Message;
use Zend\Mail\Transport\File;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\Mail\Header\ContentType;

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
		
		$emailText = <<<EOT
<p>Hello {$user->firstname} {$user->lastname},</p>
	
<p>Welcome to Mascot Laos Plattform!</p>
		
Your account has been created. Please click on below link to activate it:
		
	<p><a href="http://laosit02/user/index/register-confirm?key={$user->registration_key}&email={$user->email}">http://laosit02/user/index/register-confirm?key={$user->registration_key}&email={$user->email}</a>
	</p>
<p>
Regards,<br/>
MLA Team
</p>
<p>(<em>This Email is generated by the system automatically. Please do not reply!</em>)</p>
EOT;
		$html = new MimePart($emailText);
		$html->type = "text/html";
		
		$body = new MimeMessage();
		$body->setParts(array($html));
	
		// build message
		$message = new Message ();
		$message->addFrom ( 'mib-team@web.de' );
		$message->addTo ($user->email);
		$message->setSubject ( 'Mascot Laos Plattform Register' );
		
		$type = new ContentType();
		$type->setType('text/html');
		
		$message->getHeaders()->addHeader($type);
		$message->setBody ($emailText);
		$message->setEncoding("UTF-8");
		
		// send message
		$this->getMailTransport ()->send ( $message );
	}
}