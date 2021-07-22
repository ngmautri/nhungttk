<?php
namespace Inventory\Controller;

// use Zend\I18n\Validator\Int;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\Validator\EmailAddress;
use Zend\Mail\Message;
use Zend\View\Model\ViewModel;
use Zend\Http\Headers;
use Application\Domain\Util\Pagination\Paginator;
use MLA\Files;
use Inventory\Model\MLASparepart;
use Inventory\Model\MLASparepartTable;
use Inventory\Model\Article;
use Inventory\Model\ArticleTable;
use Inventory\Services\ArticleService;
use Inventory\Services\ArticleSearchService;
use Inventory\Model\ArticlePicture;
use Inventory\Model\ArticlePictureTable;
use Inventory\Model\ArticleCategory;
use Inventory\Model\ArticleCategoryTable;
use Inventory\Model\ArticleCategoryMember;
use Inventory\Model\ArticleCategoryMemberTable;
use Inventory\Model\ArticlePurchasing;
use Inventory\Model\ArticlePurchasingTable;
use Application\Model\DepartmentTable;
use Inventory\Model\SparepartPurchasing;
use Inventory\Model\SparepartPurchasingTable;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PurchasingController extends AbstractActionController
{

    protected $SmtpTransportService;

    protected $authService;

    protected $userTable;

    protected $articleTable;

    protected $articlePurchasingTable;

    protected $spPurchasingTable;

    protected $sparePartTable;

    protected $departmentTable;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

    /**
     * Add new purchase data
     */
    public function listAction()
    {
        $id = (int) $this->params()->fromQuery('id');
        $type = $this->params()->fromQuery('type');
        $layout = $this->params()->fromQuery('layout');

        $sp = null;
        $article = null;
        $article_purchasing = null;
        $sp_purchasing = null;

        if ($type === "article") {
            $article = $this->articleTable->get($id);
            $article_purchasing = $this->articlePurchasingTable->getPurchasingDataOf($id);
        }

        if ($type === "spare-part") {
            $sp = $this->sparePartTable->get($id);
            $sp_purchasing = $this->spPurchasingTable->getPurchasingDataOf($id);
        }
        if ($layout == "ajax") {
            $this->layout("layout/inventory/ajax");
        }
        return new ViewModel(array(
            'article' => $article,
            'sp' => $sp,
            'id' => $id,
            'type' => $type,
            'errors' => null,
            'vendor_name' => null,
            'article_purchasing' => $article_purchasing,
            'sp_purchasing' => $sp_purchasing
        ));
    }

    /**
     * Add new purchase data
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $identity = $this->authService->getIdentity();
        $user = $this->userTable->getUserByEmail($identity);
        $sp = null;
        $article = null;

        if ($request->isPost()) {

            if ($request->isPost()) {

                $type = $request->getPost('type');
                $id = $request->getPost('id');
                $vendor_name = $request->getPost('vendor_name');
                $redirectUrl = $request->getPost('redirectUrl');
                $input = null;

                if ($type == "article") {

                    $input = new ArticlePurchasing();
                    $input->article_id = $id;
                    $input->vendor_id = $request->getPost('vendor_id');

                    $input->vendor_unit_price = $request->getPost('vendor_unit_price');
                    $input->vendor_unit = $request->getPost('vendor_unit');
                    $input->vendor_article_code = $request->getPost('vendor_article_code');
                    $input->currency = $request->getPost('currency');
                    $input->is_preferred = $request->getPost('is_preferred');
                    $input->price_valid_from = $request->getPost('price_valid_from');

                    $article = $article = $this->articleTable->get($id);
                }

                if ($type == "spare-part") {

                    $input = new SparepartPurchasing();
                    $input->article_id = $id;
                    $input->vendor_id = $request->getPost('vendor_id');

                    $input->vendor_unit_price = $request->getPost('vendor_unit_price');
                    $input->vendor_unit = $request->getPost('vendor_unit');
                    $input->vendor_article_code = $request->getPost('vendor_article_code');
                    $input->currency = $request->getPost('currency');
                    $input->is_preferred = $request->getPost('is_preferred');
                    $input->price_valid_from = $request->getPost('price_valid_from');

                    $article = $article = $this->sparePartTable->get($id);
                }

                $errors = array();
                if ($input->vendor_id < 0 or $input->vendor_id == null) {
                    $errors[] = 'Please select a vendor, or create new vendor, if not found!';
                }

                if (! is_numeric($input->vendor_unit_price)) {
                    $errors[] = 'Price is not valid. It must be a number.';
                } else {
                    if ($input->vendor_unit_price <= 0) {
                        $errors[] = 'Price must be greate than 0!';
                    }
                }

                if ($input->currency == "") {
                    $errors[] = 'Please select currency!';
                }

                if ($input->vendor_unit == "") {
                    $errors[] = 'Please give vendor unit for the item!';
                }

                if (count($errors) > 0) {
                    return new ViewModel(array(
                        'redirectUrl' => $redirectUrl,
                        'article' => $article,
                        'sp' => $sp,
                        'id' => $id,
                        'type' => $type,
                        'errors' => $errors,
                        'vendor_name' => $vendor_name,
                        'submitted_purchasing' => $input
                    ));
                }

                if ($type == "article") {
                    $this->articlePurchasingTable->add($input);
                }

                if ($type == "spare-part") {
                    $this->spPurchasingTable->add($input);
                }

                $this->redirect()->toUrl($redirectUrl);
            }
        }

        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        $id = (int) $this->params()->fromQuery('id');
        $type = $this->params()->fromQuery('type');

        if ($type == "article") {
            $article = $this->articleTable->get($id);
        }

        if ($type == "spare-part") {
            $article = $this->sparePartTable->get($id);
        }

        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'article' => $article,
            'sp' => $sp,
            'id' => $id,
            'type' => $type,
            'errors' => null,
            'vendor_name' => null,
            'submitted_purchasing' => null
        ));
    }

    public function getSmtpTransportService()
    {
        return $this->SmtpTransportService;
    }

    public function setSmtpTransportService($SmtpTransportService)
    {
        $this->SmtpTransportService = $SmtpTransportService;
        return $this;
    }

    public function getAuthService()
    {
        return $this->authService;
    }

    public function setAuthService($authService)
    {
        $this->authService = $authService;
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

    public function getArticleTable()
    {
        return $this->articleTable;
    }

    public function setArticleTable(ArticleTable $articleTable)
    {
        $this->articleTable = $articleTable;
        return $this;
    }

    public function getSparePartTable()
    {
        return $this->sparePartTable;
    }

    public function setSparePartTable(MLASparepartTable $sparePartTable)
    {
        $this->sparePartTable = $sparePartTable;
        return $this;
    }

    public function getDepartmentTable()
    {
        return $this->departmentTable;
    }

    public function setDepartmentTable($departmentTable)
    {
        $this->departmentTable = $departmentTable;
        return $this;
    }

    public function getArticlePurchasingTable()
    {
        return $this->articlePurchasingTable;
    }

    public function setArticlePurchasingTable(ArticlePurchasingTable $articlePurchasingTable)
    {
        $this->articlePurchasingTable = $articlePurchasingTable;
        return $this;
    }

    public function getSpPurchasingTable()
    {
        return $this->spPurchasingTable;
    }

    public function setSpPurchasingTable(SparepartPurchasingTable $spPurchasingTable)
    {
        $this->spPurchasingTable = $spPurchasingTable;
        return $this;
    }

    // SETTER AND GETTER
}
