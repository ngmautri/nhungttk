<?php

/**
 * Configuration Module: Inventory
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
namespace Inventory;

use Zend\Mvc\ModuleRouteListener;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Inventory\Model\AssetCategory;
use Inventory\Model\AssetCategoryTable;
use Inventory\Model\AssetGroup;
use Inventory\Model\AssetGroupTable;
use Inventory\Model\MLAAsset;
use Inventory\Model\MLAAssetTable;
use Inventory\Model\AssetPicture;
use Inventory\Model\AssetPictureTable;
use Inventory\Model\MLASparepart;
use Inventory\Model\MLASparepartTable;
use Inventory\Model\SparepartPicture;
use Inventory\Model\SparepartPictureTable;
use Inventory\Model\SparepartMovement;
use Inventory\Model\SparepartMovementsTable;
use Inventory\Model\SparepartCategory;
use Inventory\Model\SparepartCategoryTable;
use Inventory\Model\SparepartCategoryMember;
use Inventory\Model\SparepartCategoryMemberTable;
use Inventory\Model\AssetCounting;
use Inventory\Model\AssetCountingTable;
use Inventory\Model\AssetCountingItem;
use Inventory\Model\AssetCountingItemTable;
use Inventory\Model\Article;
use Inventory\Model\ArticleTable;
use Inventory\Model\ArticleCategory;
use Inventory\Model\ArticleCategoryTable;
use Inventory\Model\ArticleCategoryMember;
use Inventory\Model\ArticleCategoryMemberTable;
use Inventory\Model\ArticlePicture;
use Inventory\Model\ArticlePictureTable;
use Inventory\Model\ArticleMovement;
use Inventory\Model\ArticleMovementTable;
use Inventory\Model\ArticleLastDN;
use Inventory\Model\ArticleLastDNTable;
use Inventory\Model\SparepartLastDN;
use Inventory\Model\SparepartLastDNTable;
use Inventory\Model\SparepartMinimumBalance;
use Inventory\Model\SparepartMinimumBalanceTable;
use Inventory\Model\ArticlePurchasing;
use Inventory\Model\ArticlePurchasingTable;
use Inventory\Model\SparepartPurchasing;
use Inventory\Model\SparepartPurchasingTable;
use Inventory\Model\Warehouse;
use Inventory\Model\WarehouseTable;
use Inventory\Model\MlaArticleDepartment;
use Inventory\Model\MlaArticleDepartmentTable;
use Zend\ModuleManager\ModuleManager;

class Module
{

    /*
     * The init() method is called for every module implementing this feature, on every page request,
     * and should only be used for performing lightweight tasks such as registering event listeners.
     */

    /*
     * public function init(ModuleManager $moduleManager)
     * {
     * // Remember to keep the init() method as lightweight as possible
     * $events = $moduleManager->getEventManager();
     * $events->attach('loadModules.post', array($this, 'modulesLoaded'));
     * }
     */

    /*
     * The onBootstrap() method is called for every module implementing this feature, on every page request,
     * and should only be used for performing lightweight tasks such as registering event listeners.
     */
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                )
            )
        );
    }

    /**
     * Add this function
     *
     * @return array
     */
    public function getServiceConfig()
    {
        return array();
    }
}
