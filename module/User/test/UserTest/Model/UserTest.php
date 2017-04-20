<?php

namespace UserTest\Model;

use PHPUnit_Framework_TestCase;
use Zend\Db\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\EventManager\EventManager;
use Zend\EventManager\Event;
use User\Model\UserTable;
use UserTest\Bootstrap;


use Zend\Mail\Message;
use Zend\Mail\Transport\File;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;

use Zend\Mail\Header\ContentType;
use User\Service\Acl;

class UserTest extends PHPUnit_Framework_TestCase {
 	 protected $tree;
	
 	 public function testArticleCatergoryTest() {
 	 	$list = Bootstrap::getServiceManager()->get('User\Service\ArticleCategory');
 	 	//$list = $this->articleCategoryService;
 	 	$list = $list->initCategory ();
 	 	$list = $list->updateCategory ( 42, 0 );
 	 	//var_dump($list);
 	 	$list = $list->generateJSTreeWithTotalMember ( 42 );
 	 	var_dump($list->getCategories ()[43]);
 	 
 	 }
	
 	 /*
	public function testUserTable() {
		//$userTable = Bootstrap::getServiceManager ()->get ( 'User\Model\AclRoleTable' );
		 //var_dump($userTable->getAclRoleResources());
		/*
		$results  = $userTable->initAcl();
		var_dump($results->isAccessAllowed('spare-part-controller','Inventory\Controller\Spareparts-add',null));
		*/
		
		//$userTable = Bootstrap::getServiceManager ()->get ( 'User\Model\AclRoleTable' );
		
		
		/*
		$data = array();
		$index = array();
		
		
		$results = $userTable->getRoles(0,0);
		foreach ($results as $row)
		{
			$id = $row["id"];
			$parent_id = $row->parent_id === NULL ? "NULL" :  $row->parent_id;
			$data[$id] = $row;
			$index[$parent_id][] = $id;
		}
		$this->display_tree($index, $data, "NULL", 0);
		var_dump($index);
		*/
		
	//	$userTable = Bootstrap::getServiceManager ()->get ( 'User\Model\AclUserRoleTable' );
		//$userTable = Bootstrap::getServiceManager ()->get ( 'User\Model\AclUserRoleTable' );
		
		/*
		$results  = $userTable->initCategory();
		
		$results =$results->updateCategory(1,0);
		$results =$results->generateJSTree(1);
		*/
		
		//var_dump($userTable->getRoleByUserId(39)->Count());
		//var_dump($results->getCategories()[1]);
		
		//$root= $results->categories['adm-hr-manager '];
		//$this->tree($data,'administrator');
		//echo $this->tree;
	//	}
	
	
	public function tree($data,$root){
		$tree = $data->categories[$root];
		$children = $tree['children'];
		
		if(count($children)>0)
		{
			$this->tree=$this->tree.'<li data-jstree=\'{ "opened" : true }\'>' .ucwords($root )."\n";
			$this->tree=$this->tree. '<ul>'.  "\n";
			foreach ($children as $c){
				if(count($c['children'])>0){
					$this->tree($data,$c['instance']);
					
				}else{
					$this->tree=$this->tree. '<li>' . $c['instance'] .' </li>'. "\n";
					$this->tree($data,$c['instance']);	
				}
			}
			$this->tree=$this->tree. '</ul>'.  "\n";
			
			$this->tree=$this->tree. '</li>'.  "\n";
		}
		
	}
	
	/*
	public function testDI() {
		$callback = function ($event) {
			echo "An event has happend \n";
			var_dump ( $event->getName () );
			var_dump ( $event->getParams () );
		};
		
		$eventManager = new EventManager ();
		$eventManager->attach ( 'eventName', $callback );
		echo "\nRaise an event\n";
		$eventManager->trigger ( 'eventName', null, array (
				'one' => 1,
				'two' => 2 
		) );
	}
	*/
	
	
	public function testSendingMail() {
		
$emailText = <<<EOT
	
<p>Welcome Mascot Laos Plattform!</p>
		
Your account is created.<br/>
click on below link to activate your account:
<p>
Regards<br/>
MLA Team
</p>
<p>(<em>This Email is generated by the system.</em>)</p>
EOT;

		$html = new MimePart($emailText);
		//$html->type = Mime::TYPE_HTML;
		
		$body = new MimeMessage();
		$body->setParts(array($html));
		
		// build message
		$message = new Message ();
		$message->addFrom ( 'mla-app@outlook.com' );
		$message->addTo ('ngmautri@outlook.com');
		$message->setSubject ( 'Mascot Laos Plattform Register' );
		
		$type = new ContentType();
		$type->setType('text/html');
		
		$message->getHeaders()->addHeader($type);
		$message->setBody ($emailText);
		
		$mailTransport = Bootstrap::getServiceManager ()->get ( 'mla-web@outlook.com' );
		
		// send message
		$mailTransport->send ( $message );
		echo $emailText;
	
	}
	
	
	function display_tree($index,$data,$parent_id, $level)
	{
			$parent_id = $parent_id === NULL ? "NULL" : $parent_id;
		if (isset($index[$parent_id])) {
			foreach ($index[$parent_id] as $id) {
				if(isset($data[$parent_id])):
					echo $level . ". " . str_repeat(" - ", $level) . $data[$parent_id]->role .'//'.$data[$id]->role . "==". $data[$id]->path . "\n";
				else:
					echo $level . ". " . str_repeat(" - ", $level) .$data[$id]->role . "==". $data[$id]->path . "\n";
				endif;
				$this->display_tree($index,$data,$id, $level+1);
			}
		}
	}
	
}

