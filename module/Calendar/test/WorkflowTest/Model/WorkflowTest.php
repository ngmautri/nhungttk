<?php
namespace WorkflowTest;

use PHPUnit_Framework_TestCase;
use Zend\Db\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\EventManager\EventManager;
use Zend\EventManager\Event;
use User\Model\UserTable;
use WorkflowTest\Bootstrap;
use Zend\Mail\Message;
use Zend\Mail\Transport\File;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mail\Header\ContentType;
use User\Service\Acl;

class WorkflowTest extends PHPUnit_Framework_TestCase
{

    protected $tree;

    /*
     * public function testArticleCatergoryTest() {
     * $list = Bootstrap::getServiceManager()->get('User\Service\ArticleCategory');
     * //$list = $this->articleCategoryService;
     * $list = $list->initCategory ();
     * $list = $list->updateCategory ( 42, 0 );
     * //var_dump($list);
     * $list = $list->generateJSTreeWithTotalMember ( 42 );
     * var_dump($list->getCategories ()[43]);
     *
     * }
     */
    public function testWorflowTable()
    {
        // $tbl = Bootstrap::getServiceManager ()->get ( 'Workflow\Model\WorkflowNodeTable' );
        $service = Bootstrap::getServiceManager()->get('Workflow\Service\WorkflowService');
        $service->init();
        $service->updateCategory(1, 0);

        // var_dump($service->getChildNodes(1));
        var_dump($service->get(2));
        // $service->fire(4);

        // var_dump(count($service->getCategories()[1]));
        // $service->generateJSTree(1);
        // var_dump($service->getJsTree());
    }
}

