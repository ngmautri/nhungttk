<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use Zend\Barcode\Barcode;
use Application\Entity\NmtInventoryItem;
use Application\Entity\NmtInventoryItemPicture;
use User\Model\UserTable;
use Application\Domain\Util\Pagination\Paginator;

/*
 * Control Panel Controller
 */
class CategoryController extends AbstractActionController
{

    protected $doctrineEM;

    protected $userTable;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function showAction()
    {
        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $redirectUrl = $request->getHeader('Referer')->getUri();

        $item_id = (int) $this->params()->fromQuery('item_id');
        $item = $this->doctrineEM->find('Application\Entity\NmtInventoryItem', $item_id);
        $pictures = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemPicture')->findBy(array(
            "item" => $item_id
        ));
        return new ViewModel(array(
            'item' => $item,
            'pictures' => $pictures,
            'back' => $redirectUrl
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function addAction()
    {
        $identity = $this->identity();
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $identity
        ));
        $request = $this->getRequest();
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        if ($request->isPost()) {

            $item_sku = $request->getPost('item_sku');
            $item_name = $request->getPost('item_name');
            $item_name_en = $request->getPost('item_name_en');
            $item_description = $request->getPost('item_description');
            $item_code = $request->getPost('item_code');
            $item_barcode = $request->getPost('item_barcode');
            $item_type = $request->getPost('item_type');
            $item_category = $request->getPost('item_category');
            $item_uom = $request->getPost('item_uom');
            $item_leadtime = $request->getPost('item_leadtime');

            $is_active = $request->getPost('is_locked');
            $is_stocked = $request->getPost('is_locked');
            $is_purchased = $request->getPost('is_locked');
            $is_fixed_asset = $request->getPost('is_locked');

            $errors = array();

            if ($item_sku === '' or $item_sku === null) {
                $errors[] = 'Please give ID';
            }

            if ($item_name === '' or $item_name === null) {
                $errors[] = 'Please give item name';
            }

            $r = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findBy(array(
                'itemSku' => $item_sku
            ));

            if (count($r) >= 1) {
                $errors[] = $item_sku . ' exists';
            }

            if (count($errors) > 0) {
                return new ViewModel(array(
                    'errors' => $errors
                ));
            }

            // No Error

            $entity = new NmtInventoryItem();
            $entity->setItemSku($item_sku);
            $entity->setItemName($item_name);
            $entity->setItemNameForeign($item_name_en);
            $entity->setItemDescription($item_description);
            $entity->setBarcode($item_barcode);
            $entity->setItemType($item_type);

            $entity->setItemCategory($item_category);
            $entity->setUom($item_uom);

            $entity->setManufacturerCode($item_code);

            $entity->setIsStocked($is_stocked);
            $entity->setIsActive($is_active);
            $entity->setIsPurchased($is_purchased);
            $entity->setIsFixedAsset($is_fixed_asset);

            $entity->setLeadTime($item_leadtime);

            $company_id = 1;
            $company = $this->doctrineEM->find('Application\Entity\NmtApplicationCompany', $company_id);
            $entity->setCompany($company);

            $entity->setCreatedOn(new \DateTime());
            $entity->setCreatedBy($u);
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();

            $redirectUrl = $request->getPost('redirectUrl');
            $this->redirect()->toUrl($redirectUrl);
        }

        /*
         * if ($request->isXmlHttpRequest ()) {
         * $this->layout ( "layout/inventory/ajax" );
         * }
         */
        $company_id = (int) $this->params()->fromQuery('company_id');
        $company = $this->doctrineEM->find('Application\Entity\NmtApplicationCompany', $company_id);
        $countries = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCountry')->findAll();
        return new ViewModel(array(
            'errors' => null,
            'identity' => $u,
            'company' => $company,
            'countries' => $countries,
            'redirectUrl' => $redirectUrl
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $sort = array();
        $criteria = array();

        $sort_by = $this->params()->fromQuery('sort_by');
        $item_status = $this->params()->fromQuery('item_status');
        $item_type = $this->params()->fromQuery('item_type');

        if ($item_status == null) :
            $item_status = "Activated";
		
		endif;

        if ($sort_by == null) :
            $sort_by = "itemName";		
		endif;

        $sort = array(
            $sort_by => "ASC"
        );

        if (! $item_type == null) {
            $criteria = array(
                "itemType" => $item_type
            );
        }

        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 15;
        } else {
            $resultsPerPage = $this->params()->fromQuery('perPage');
        }
        ;

        if (is_null($this->params()->fromQuery('page'))) {
            $page = 1;
        } else {
            $page = $this->params()->fromQuery('page');
        }
        ;

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findBy($criteria, $sort);
        $total_records = count($list);
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findBy($criteria, $sort, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1);
        }

        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'sort_by' => $sort_by,
            'item_status' => $item_status,
            'per_pape' => $resultsPerPage,
            'item_type' => $item_type
        ));
    }

    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }

    public function getUserTable()
    {
        return $this->userTable;
    }

    public function setUserTable($userTable)
    {
        $this->userTable = $userTable;
        return $this;
    }
}
