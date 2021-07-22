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
use Zend\Validator\Date;
use Zend\Validator\EmailAddress;
use Zend\Mail\Message;
use Zend\View\Model\ViewModel;
use Zend\Http\Headers;
use Application\Domain\Util\Pagination\Paginator;
use MLA\Files;
use Inventory\Model\SparepartPicture;
use Inventory\Model\SparepartPictureTable;
use Inventory\Model\MLASparepart;
use Inventory\Model\MLASparepartTable;
use Inventory\Model\SparepartCategoryMember;
use Inventory\Model\SparepartCategoryMemberTable;
use Inventory\Model\SparepartCategoryTable;
use Inventory\Model\SparepartMovement;
use Inventory\Model\SparepartMovementsTable;
use Inventory\Services\SparepartService;
use Inventory\Model\SparepartMinimumBalance;
use Inventory\Model\SparepartMinimumBalanceTable;
use Procurement\Model\PurchaseRequestCartItem;
use Procurement\Model\PurchaseRequestCartItemTable;
use Procurement\Model\PurchaseRequestItemTable;

class SparepartsController extends AbstractActionController
{

    protected $authService;

    protected $SmtpTransportService;

    protected $sparePartService;

    protected $userTable;

    protected $sparePartTable;

    protected $sparePartPictureTable;

    protected $sparepartMovementsTable;

    protected $sparePartCategoryTable;

    protected $sparePartCategoryMemberTable;

    protected $purchaseRequestCartItemTable;

    protected $spMinimumBalanceTable;

    protected $massage = 'NULL';

    private $prItemTable;

    protected $movement_type_issue = array(
        '01' => 'FOR_REPLACEMENT',
        '02' => 'FOR_IE_PROJECT',
        '03' => 'FOR_INSTALLMENT'
    );

    protected $locations = array(
        "LINE-01",
        "LINE-02",
        "LINE-03",
        "LINE-04",
        "LINE-05",
        "LINE-06",
        "LINE-07",
        "LINE-08",
        "LINE-10",
        "LINE-09",
        "LINE-A",
        "LINE-SPE",
        "CUTTING",
        "OTHER"
    );

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

    /**
     * create new spare part
     */
    public function addAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            if ($request->isPost()) {
                $redirectUrl = $request->getPost('redirectUrl');

                $input = new MLASparepart();
                $input->name = $request->getPost('name');
                $input->name_local = $request->getPost('name_local');

                $input->description = $request->getPost('description');
                $input->code = $request->getPost('code');
                $input->tag = $request->getPost('tag');

                $input->location = $request->getPost('location');
                $input->comment = $request->getPost('comment');

                $category_id = (int) $request->getPost('category_id');

                $errors = array();

                // tag must be unique

                if ($this->sparePartTable->isTagExits($input->tag) === true) {
                    $errors[] = 'Sparepart with tag number "' . $input->tag . '" exits already in database';
                }

                if ($input->name == '') {
                    $errors[] = 'Please give spare-part name';
                }

                if (count($errors) > 0) {
                    return new ViewModel(array(
                        'errors' => $errors,
                        'redirectUrl' => $redirectUrl,
                        'category_id' => $category_id,
                        'sparepart' => $input
                    ));
                }

                $newId = $this->sparePartTable->add($input);
                $root_dir = $this->sparePartService->getPicturesPath();

                // $files = $request->getFiles ()->toArray ();

                $pictureUploadListener = $this->getServiceLocator()->get('Inventory\Listener\PictureUploadListener');
                $this->getEventManager()->attachAggregate($pictureUploadListener);

                $id = $newId;

                foreach ($_FILES["pictures"]["error"] as $key => $error) {
                    if ($error == UPLOAD_ERR_OK) {
                        $tmp_name = $_FILES["pictures"]["tmp_name"][$key];

                        $ext = strtolower(pathinfo($_FILES["pictures"]["name"][$key], PATHINFO_EXTENSION));

                        if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'gif' || $ext == 'png') {

                            $checksum = md5_file($tmp_name);

                            if (! $this->sparePartPictureTable->isChecksumExits($id, $checksum)) {

                                $name = md5($id . $checksum . uniqid(microtime())) . '.' . $ext;
                                $folder = $root_dir . DIRECTORY_SEPARATOR . $name[0] . $name[1] . DIRECTORY_SEPARATOR . $name[2] . $name[3] . DIRECTORY_SEPARATOR . $name[4] . $name[5];

                                if (! is_dir($folder)) {
                                    mkdir($folder, 0777, true); // important
                                }

                                $ftype = $_FILES["pictures"]["type"][$key];
                                move_uploaded_file($tmp_name, "$folder/$name");

                                // add pictures
                                $pic = new SparepartPicture();
                                $pic->url = "$folder/$name";
                                $pic->filetype = $ftype;
                                $pic->sparepart_id = $id;
                                $pic->filename = "$name";
                                $pic->folder = "$folder";
                                $pic->checksum = $checksum;

                                $this->sparePartPictureTable->add($pic);

                                // trigger uploadPicture
                                $this->getEventManager()->trigger('uploadPicture', __CLASS__, array(
                                    'picture_name' => $name,
                                    'pictures_dir' => $folder
                                ));
                            }
                        }
                    }
                }

                // add category
                if ($category_id > 1) {
                    $m = new SparepartCategoryMember();
                    $m->sparepart_id = $newId;
                    $m->sparepart_cat_id = $category_id;
                    $this->sparePartCategoryMemberTable->add($m);
                }

                $this->redirect()->toUrl($redirectUrl);
            }
        }

        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        $category_id = (int) $this->params()->fromQuery('category_id');

        // add category
        if ($category_id > 1) {
            $category = $this->sparePartCategoryTable->get($category_id);
        } else {
            $category = null;
        }

        return new ViewModel(array(
            'message' => 'Add new Sparepart',
            'category' => $category,
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'sparepart' => null
        ));
    }

    /**
     * Edit Spare part
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $id = $request->getPost('id');

            $input = new MLASparepart();
            $input->id = $id;
            $input->name = $request->getPost('name');
            $input->name_local = $request->getPost('name_local');

            $input->description = $request->getPost('description');
            $input->code = $request->getPost('code');
            $input->tag = $request->getPost('tag');

            $input->location = $request->getPost('location');
            $input->comment = $request->getPost('comment');

            $errors = array();

            if ($input->name == '') {
                $errors[] = 'Please give spare-part name';
            }

            $redirectUrl = $request->getPost('redirectUrl');

            if (count($errors) > 0) {
                // return current sp
                $sparepart = $this->sparePartTable->get($input->id);

                return new ViewModel(array(
                    'errors' => $errors,
                    'redirectUrl' => $redirectUrl,
                    'sparepart' => $sparepart
                ));
            }

            $this->sparePartTable->update($input, $input->id);
            $root_dir = $this->sparePartService->getPicturesPath();

            $pictureUploadListener = $this->getServiceLocator()->get('Inventory\Listener\PictureUploadListener');
            $this->getEventManager()->attachAggregate($pictureUploadListener);

            foreach ($_FILES["pictures"]["error"] as $key => $error) {
                if ($error == UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES["pictures"]["tmp_name"][$key];

                    $ext = strtolower(pathinfo($_FILES["pictures"]["name"][$key], PATHINFO_EXTENSION));

                    if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'gif' || $ext == 'png') {

                        $checksum = md5_file($tmp_name);

                        if (! $this->sparePartPictureTable->isChecksumExits($id, $checksum)) {

                            $name = md5($id . $checksum . uniqid(microtime())) . '.' . $ext;
                            $folder = $root_dir . DIRECTORY_SEPARATOR . $name[0] . $name[1] . DIRECTORY_SEPARATOR . $name[2] . $name[3] . DIRECTORY_SEPARATOR . $name[4] . $name[5];

                            if (! is_dir($folder)) {
                                mkdir($folder, 0777, true); // important
                            }

                            $ftype = $_FILES["pictures"]["type"][$key];
                            move_uploaded_file($tmp_name, "$folder/$name");

                            // add pictures
                            $pic = new SparepartPicture();
                            $pic->url = "$folder/$name";
                            $pic->filetype = $ftype;
                            $pic->sparepart_id = $id;
                            $pic->filename = "$name";
                            $pic->folder = "$folder";
                            $pic->checksum = $checksum;

                            $this->sparePartPictureTable->add($pic);

                            // trigger uploadPicture
                            $this->getEventManager()->trigger('uploadPicture', __CLASS__, array(
                                'picture_name' => $name,
                                'pictures_dir' => $folder
                            ));
                        }
                    }
                }
            }

            $this->redirect()->toUrl($redirectUrl);

            // return $this->redirect ()->toRoute ( 'Spareparts_Category');
        }

        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        $id = (int) $this->params()->fromQuery('id');
        $sparepart = $this->sparePartTable->get($id);

        return new ViewModel(array(
            'sparepart' => $sparepart,
            'redirectUrl' => $redirectUrl,
            'errors' => null
        ));
    }

    /**
     * Edit Spare part
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function uploadPictureAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $id = $request->getPost('id');
            $root_dir = $this->sparePartService->getPicturesPath();

            // $files = $request->getFiles ()->toArray ();

            $pictureUploadListener = $this->getServiceLocator()->get('Inventory\Listener\PictureUploadListener');
            $this->getEventManager()->attachAggregate($pictureUploadListener);

            foreach ($_FILES["pictures"]["error"] as $key => $error) {
                if ($error == UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES["pictures"]["tmp_name"][$key];

                    $ext = strtolower(pathinfo($_FILES["pictures"]["name"][$key], PATHINFO_EXTENSION));

                    if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'gif' || $ext == 'png') {

                        $checksum = md5_file($tmp_name);

                        if (! $this->sparePartPictureTable->isChecksumExits($id, $checksum)) {

                            $name = md5($id . $checksum . uniqid(microtime())) . '.' . $ext;
                            $folder = $root_dir . DIRECTORY_SEPARATOR . $name[0] . $name[1] . DIRECTORY_SEPARATOR . $name[2] . $name[3] . DIRECTORY_SEPARATOR . $name[4] . $name[5];

                            if (! is_dir($folder)) {
                                mkdir($folder, 0777, true); // important
                            }

                            $ftype = $_FILES["pictures"]["type"][$key];
                            move_uploaded_file($tmp_name, "$folder/$name");

                            // add pictures
                            $pic = new SparepartPicture();
                            $pic->url = "$folder/$name";
                            $pic->filetype = $ftype;
                            $pic->sparepart_id = $id;
                            $pic->filename = "$name";
                            $pic->folder = "$folder";
                            $pic->checksum = $checksum;

                            $this->sparePartPictureTable->add($pic);

                            // trigger uploadPicture
                            $this->getEventManager()->trigger('uploadPicture', __CLASS__, array(
                                'picture_name' => $name,
                                'pictures_dir' => $folder
                            ));
                        }
                    }
                }
            }

            $redirectUrl = $request->getPost('redirectUrl');
            $this->redirect()->toUrl($redirectUrl);

            // return $this->redirect ()->toRoute ( 'Spareparts_Category');
        }

        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        $id = (int) $this->params()->fromQuery('id');
        $sparepart = $this->sparePartTable->get($id);

        return new ViewModel(array(
            'sparepart' => $sparepart,
            'redirectUrl' => $redirectUrl,
            'errors' => null
        ));
    }

    /**
     * Upload resized pictures
     *
     * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
     */
    public function uploadPicture1Action()
    {
        $request = $this->getRequest();
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        if ($request->isPost()) {

            $pictures = $_POST['pictures'];
            $id = $_POST['sparepart_id'];

            foreach ($pictures as $p) {

                $filetype = $p[0];
                if (preg_match('/(jpg|jpeg)$/', $filetype)) {
                    $ext = 'jpg';
                } else if (preg_match('/(gif)$/', $filetype)) {
                    $ext = 'gif';
                } else if (preg_match('/(png)$/', $filetype)) {
                    $ext = 'png';
                }

                $tmp_name = md5($id . uniqid(microtime())) . '.' . $ext;

                // remove "data:image/png;base64,"
                $uri = substr($p[1], strpos($p[1], ",") + 1);

                // save to file
                file_put_contents($tmp_name, base64_decode($uri));

                $checksum = md5_file($tmp_name);

                $root_dir = $this->sparePartService->getPicturesPath();

                $pictureUploadListener = $this->getServiceLocator()->get('Inventory\Listener\PictureUploadListener');
                $this->getEventManager()->attachAggregate($pictureUploadListener);

                if (! $this->sparePartPictureTable->isChecksumExits($id, $checksum)) {
                    $name = md5($id . $checksum . uniqid(microtime())) . '.' . $ext;

                    $folder = $root_dir . DIRECTORY_SEPARATOR . $name[0] . $name[1] . DIRECTORY_SEPARATOR . $name[2] . $name[3] . DIRECTORY_SEPARATOR . $name[4] . $name[5];

                    if (! is_dir($folder)) {
                        mkdir($folder, 0777, true); // important
                    }

                    rename($tmp_name, "$folder/$name");

                    // add pictures
                    $pic = new SparepartPicture();
                    $pic->url = "$folder/$name";
                    $pic->filetype = $filetype;
                    $pic->sparepart_id = $id;
                    $pic->filename = "$name";
                    $pic->folder = "$folder";
                    $pic->checksum = $checksum;
                    $this->sparePartPictureTable->add($pic);

                    // trigger uploadPicture
                    $this->getEventManager()->trigger('uploadPicture', __CLASS__, array(
                        'picture_name' => $name,
                        'pictures_dir' => $folder
                    ));
                }
            }

            $data = array();
            $data['id'] = $id;
            // $data['filetype'] = $filetype;

            $response = $this->getResponse();
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            return $response;
        }

        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        $id = (int) $this->params()->fromQuery('id');
        $sparepart = $this->sparePartTable->get($id);

        return new ViewModel(array(
            'sparepart' => $sparepart,
            'redirectUrl' => $redirectUrl,
            'errors' => null
        ));
    }

    /**
     * Issue sparepart
     */
    public function issueAction()
    {
        $request = $this->getRequest();
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        if ($request->isPost()) {

            $input = new SparepartMovement();
            $input->sparepart_id = $request->getPost('sparepart_id');
            $input->movement_date = $request->getPost('movement_date');

            $input->sparepart_id = $request->getPost('sparepart_id');
            $input->asset_id = $request->getPost('asset_id');
            $input->asset_name = $request->getPost('asset');

            $input->quantity = $request->getPost('quantity');

            $input->flow = 'OUT';

            $input->reason = $request->getPost('reason');
            $input->requester = $request->getPost('requester');
            $input->comment = $request->getPost('comment');
            $input->created_on = $request->getPost('created_on');
            $input->movement_type = $request->getPost('movement_type');
            $input->asset_location = $request->getPost('asset_location');

            $instock = $request->getPost('instock');
            $redirectUrl = $request->getPost('redirectUrl');

            $pending_pr_item = $this->prItemTable->getPendingPRItemsOfSparepart($input->sparepart_id);
            $total_confirmed_balance = 0;
            if (count($pending_pr_item) > 0) {
                foreach ($pending_pr_item as $item) {
                    $total_confirmed_balance = $total_confirmed_balance + $item->confirmed_balance;
                }
            }

            // validator.
            $validator = new Date();

            $errors = array();

            if (! $validator->isValid($input->movement_date)) {
                $errors[] = 'Transaction date format is not correct!';
            }

            // Fixed it by going to php.ini and uncommenting extension=php_intl.dll
            $validator = new Int();

            if (! $validator->isValid($input->quantity)) {
                $errors[] = 'Quantity is not valid. It must be a number.';
            } else {

                if ($input->quantity <= 0) {
                    $errors[] = 'Quantity must be greater than 0!';
                }

                if ($input->quantity > $instock + $total_confirmed_balance) {
                    $errors[] = 'Issue quantity is: ' . $input->quantity . ' pcs, which is bigger than availabe stock';
                }
            }

            if (count($errors) > 0) {

                $id = (int) $request->getPost('sparepart_id');
                $sp = $this->sparePartTable->get($id);
                $pictures = $this->sparePartPictureTable->getSparepartPicturesById($id);
                $asset_name = $request->getPost('asset_name');

                return new ViewModel(array(
                    'sp' => $sp,
                    'pictures' => $pictures,
                    'instock' => $instock,
                    'errors' => $errors,
                    'redirectUrl' => $redirectUrl,
                    'movement' => $input,
                    'pending_pr_item' => $pending_pr_item,
                    'total_confirmed_balance' => $total_confirmed_balance,
                    'asset_name' => $asset_name,
                    'movement_types' => $this->movement_type_issue,
                    'locations' => $this->locations
                ));
            } else {

                // Validated
                $this->sparepartMovementsTable->add($input);

                $this->redirect()->toUrl($redirectUrl);
            }
        }

        $id = (int) $this->params()->fromQuery('sparepart_id');
        $sp = $this->sparePartTable->getSP($id);
        $pictures = $this->sparePartPictureTable->getSparepartPicturesById($id);
        $inflow = 0;
        $outflow = 0;

        if ($sp != null) {
            ;
            $inflow = $sp->total_inflow;
            $outflow = $sp->total_outflow;
        }

        $instock = $inflow - $outflow;

        $pending_pr_item = $this->prItemTable->getPendingPRItemsOfSparepart($id);
        $total_confirmed_balance = 0;
        if (count($pending_pr_item) > 0) {
            foreach ($pending_pr_item as $item) {
                $total_confirmed_balance = $total_confirmed_balance + $item->confirmed_balance;
            }
        }

        return new ViewModel(array(
            'sp' => $sp,
            'pictures' => $pictures,
            'instock' => $instock,
            'errors' => null,
            'redirectUrl' => $redirectUrl,
            'movement' => null,
            'pending_pr_item' => $pending_pr_item,
            'total_confirmed_balance' => $total_confirmed_balance,
            'asset_name' => null,
            'movement_types' => $this->movement_type_issue,
            'locations' => $this->locations
        ));
    }

    /**
     *
     * @todo: Check Pending PR for this Spare Part
     * receive spare part
     */
    public function receiveAction()
    {
        $request = $this->getRequest();
        // $redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();

        if ($request->isPost()) {

            $request = $this->getRequest();

            if ($request->isPost()) {

                $input = new SparepartMovement();
                $input->sparepart_id = $request->getPost('sparepart_id');
                $input->movement_date = $request->getPost('movement_date');

                $input->sparepart_id = $request->getPost('sparepart_id');
                $input->asset_id = $request->getPost('asset_id');
                $input->quantity = $request->getPost('quantity');

                $input->flow = 'IN';

                $input->reason = $request->getPost('reason');
                $input->requester = $request->getPost('requester');
                $input->comment = $request->getPost('comment');
                $input->created_on = $request->getPost('created_on');
                $email = $request->getPost('email');

                $instock = $request->getPost('instock');
                $redirectUrl = $request->getPost('redirectUrl');

                $errors = array();

                // validator.
                $validator = new Date();

                if (! $validator->isValid($input->movement_date)) {
                    $errors[] = 'Transaction date format is not correct!';
                }

                // Fixed it by going to php.ini and uncommenting extension=php_intl.dll
                $validator = new Int();

                if (! $validator->isValid($input->quantity)) {
                    $errors[] = 'Quantity is not valid. It must be a number.';
                } else {
                    if ($input->quantity <= 0) {
                        $errors[] = 'Quantity must be greater than 0!';
                    }
                }

                $validator = new EmailAddress();
                if (! $validator->isValid($email)) {
                    $errors[] = 'Email is not correct.';
                }

                $id = (int) $request->getPost('sparepart_id');
                $sp = $this->sparePartTable->get($id);
                $pictures = $this->sparePartPictureTable->getSparepartPicturesById($id);

                if (count($errors) > 0) {
                    return new ViewModel(array(
                        'sp' => $sp,
                        'pictures' => $pictures,
                        'instock' => $instock,
                        'errors' => $errors,
                        // 'redirectUrl' => $redirectUrl,
                        'movement' => $input
                    ));
                } else {

                    // Validated
                    $newId = $this->sparepartMovementsTable->add($input);

                    /*
                     * do not send email
                     * if ($newId > 0) {
                     * // sent email;
                     *
                     * $transport = $this->getServiceLocator ()->get ( 'SmtpTransportService' );
                     * $message = new Message ();
                     * $body = $input->quantity . ' pcs of Spare parts ' . $sp->name . ' (ID' . $sp->tag . ') received!';
                     * $message->addTo ( $email )->addFrom ( 'mib-team@web.de' )->setSubject ( 'Mascot Laos - Spare Part Movements' )->setBody ( $body );
                     * $transport->send ( $message );
                     * }
                     */

                    $redirectUrl = $request->getPost('redirectUrl');
                    $this->redirect()->toUrl($redirectUrl);
                }
            }
        }

        $id = (int) $this->params()->fromQuery('sparepart_id');
        $sp = $this->sparePartTable->get($id);
        $pictures = $this->sparePartPictureTable->getSparepartPicturesById($id);
        $inflow = $this->sparepartMovementsTable->getTotalInflowOf($id);
        $outflow = $this->sparepartMovementsTable->getTotalOutflowOf($id);
        $instock = $inflow - $outflow;
        $pending_pr_item = $this->sparePartTable->getPendingPRItems($id);

        return new ViewModel(array(
            'sp' => $sp,
            'pictures' => $pictures,
            'instock' => $instock,
            'errors' => null,
            // 'redirectUrl' => $redirectUrl,
            'movement' => null,
            'pending_pr_item' => $pending_pr_item,
            'paginator' => null
        ));
    }

    /**
     *
     * @todo: Check Pending PR for this Spare Part
     * receive spare part
     */
    public function grAction()
    {
        $request = $this->getRequest();
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        if ($request->isPost()) {

            $request = $this->getRequest();

            if ($request->isPost()) {

                $input = new SparepartMovement();
                $input->sparepart_id = $request->getPost('sparepart_id');
                $input->movement_date = $request->getPost('movement_date');

                $input->sparepart_id = $request->getPost('sparepart_id');
                $input->asset_id = $request->getPost('asset_id');
                $input->quantity = $request->getPost('quantity');

                $input->flow = 'IN';

                $input->reason = $request->getPost('reason');
                $input->requester = $request->getPost('requester');
                $input->comment = $request->getPost('comment');
                $input->created_on = $request->getPost('created_on');
                $email = $request->getPost('email');

                $instock = $request->getPost('instock');
                $redirectUrl = $request->getPost('redirectUrl');

                $errors = array();

                // validator.
                $validator = new Date();

                if (! $validator->isValid($input->movement_date)) {
                    $errors[] = 'Transaction date format is not correct!';
                }

                // Fixed it by going to php.ini and uncommenting extension=php_intl.dll
                $validator = new Int();

                if (! $validator->isValid($input->quantity)) {
                    $errors[] = 'Quantity is not valid. It must be a number.';
                }

                $validator = new EmailAddress();
                if (! $validator->isValid($email)) {
                    $errors[] = 'Email is not correct.';
                }

                $id = (int) $request->getPost('sparepart_id');
                $sp = $this->sparePartTable->get($id);
                $pictures = $this->sparePartPictureTable->getSparepartPicturesById($id);

                if (count($errors) > 0) {
                    return new ViewModel(array(
                        'sp' => $sp,
                        'pictures' => $pictures,
                        'instock' => $instock,
                        'errors' => $errors,
                        'redirectUrl' => $redirectUrl,
                        'movement' => $input
                    ));
                } else {

                    // Validated
                    $newId = $this->sparepartMovementsTable->add($input);

                    /*
                     * do not send email
                     * if ($newId > 0) {
                     * // sent email;
                     *
                     * $transport = $this->getServiceLocator ()->get ( 'SmtpTransportService' );
                     * $message = new Message ();
                     * $body = $input->quantity . ' pcs of Spare parts ' . $sp->name . ' (ID' . $sp->tag . ') received!';
                     * $message->addTo ( $email )->addFrom ( 'mib-team@web.de' )->setSubject ( 'Mascot Laos - Spare Part Movements' )->setBody ( $body );
                     * $transport->send ( $message );
                     * }
                     */

                    $redirectUrl = $request->getPost('redirectUrl');
                    $this->redirect()->toUrl($redirectUrl);
                }
            }
        }

        $id = (int) $this->params()->fromQuery('sparepart_id');
        $sp = $this->sparePartTable->get($id);
        $pictures = $this->sparePartPictureTable->getSparepartPicturesById($id);
        $inflow = $this->sparepartMovementsTable->getTotalInflowOf($id);
        $outflow = $this->sparepartMovementsTable->getTotalOutflowOf($id);
        $instock = $inflow - $outflow;
        $pending_pr_item = $this->prItemTable->getPendingPRItemsOfSparepart($id);

        return new ViewModel(array(
            'sp' => $sp,
            'pictures' => $pictures,
            'instock' => $instock,
            'errors' => null,
            'redirectUrl' => $redirectUrl,
            'movement' => null,
            'pending_pr_item' => $pending_pr_item,
            'paginator' => null
        ));
    }

    /**
     * Issue sparepart
     */
    public function consumptionAction()
    {
        $id = (int) $this->params()->fromQuery('asset_id');
        $sp_consumption = $this->sparePartTable->getSPConsumptionByAsset($id);

        return new ViewModel(array(
            'movements' => $sp_consumption
        ));
    }

    /**
     * Issue sparepart
     */
    public function reportAction()
    {
        $id = (int) $this->params()->fromQuery('sparepart_id');
        $year = (int) $this->params()->fromQuery('year');
        $layout = $this->params()->fromQuery('layout');

        if ($year == null) :
            $year = 2016;
		endif;

        $sp_consumption = $this->sparepartMovementsTable->getMovementSummary($year, $id);
        $sp = $this->sparePartTable->get($id);

        if ($layout == "ajax") {
            $this->layout("layout/inventory/ajax");
        }

        return new ViewModel(array(
            'movements' => $sp_consumption,
            'year' => $year,
            'sp' => $sp
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function addCategoryAction()
    {
        $request = $this->getRequest();
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        if ($request->isPost()) {

            $sparepart_id = (int) $request->getPost('id');
            $categories = $request->getPost('category');

            if (count($categories) > 0) {

                foreach ($categories as $cat) {
                    $member = new SparepartCategoryMember();
                    $member->sparepart_cat_id = $cat;
                    $member->sparepart_id = $sparepart_id;

                    if ($this->sparePartCategoryMemberTable->isMember($sparepart_id, $cat) == false) {
                        $this->sparePartCategoryMemberTable->add($member);
                    }
                }

                /*
                 * return new ViewModel ( array (
                 * 'sparepart' => null,
                 * 'categories' => $categories,
                 *
                 * ) );
                 */
                $redirectUrl = $request->getPost('redirectUrl');
                $this->redirect()->toUrl($redirectUrl);
            }
        }

        $id = (int) $this->params()->fromQuery('id');
        $sparepart = $this->sparePartTable->get($id);

        $categories = $this->sparePartCategoryTable->getCategories();

        return new ViewModel(array(
            'sparepart' => $sparepart,
            'categories' => $categories,
            'redirectUrl' => $redirectUrl
        ));
    }

    /**
     * List all spare parts
     */
    public function listAction()
    {
        $output = $this->params()->fromQuery('output');

        if ($output === 'csv') {

            $fh = fopen('php://memory', 'w');
            // $myfile = fopen('ouptut.csv', 'a+');

            $h = array();
            $h[] = "CAGEGORY";
            $h[] = "TAG";
            $h[] = "NAME";
            $h[] = "CODE";
            $h[] = "LOCATION";
            $h[] = "TOTAL IN";
            $h[] = "TOTAL OUT";
            $h[] = "CURRENT BALANCE";
            $h[] = "MIN. BALANCE";

            $delimiter = ";";

            fputcsv($fh, $h, $delimiter, '"');
            // fputs($fh, implode($h, ',')."\n");

            $spareparts = $this->sparePartCategoryMemberTable->getAllSP(0, 0);

            foreach ($spareparts as $m) {
                $l = array();
                $l[] = (string) $m->cat_name;
                $l[] = (string) '"' . $m->tag . '"';

                $name = (string) $m->name;

                $name === '' ? $name = "-" : $name;

                $name = str_replace(',', '', $name);
                $name = str_replace(';', '', $name);

                $l[] = $name;
                $l[] = (string) $m->code;
                $l[] = (string) $m->location;
                $l[] = (string) $m->total_inflow;
                $l[] = (string) $m->total_outflow;
                $l[] = (string) $m->current_balance;
                $l[] = (string) $m->minimum_balance;

                fputcsv($fh, $l, $delimiter, '"');
                // fputs($fh, implode($l, ',')."\n");
            }

            $fileName = 'spareparts-' . date("m-d-Y") . '-' . date("h:i:sa") . '.csv';
            fseek($fh, 0);
            $output = stream_get_contents($fh);
            // file_put_contents($fileName, $output);

            $response = $this->getResponse();
            $headers = new Headers();

            $headers->addHeaderLine('Content-Type: text/csv');
            // $headers->addHeaderLine ( 'Content-Type: application/vnd.ms-excel; charset=UTF-8' );

            $headers->addHeaderLine('Content-Disposition: attachment; filename="' . $fileName . '"');
            $headers->addHeaderLine('Content-Description: File Transfer');
            $headers->addHeaderLine('Content-Transfer-Encoding: binary');
            $headers->addHeaderLine('Content-Encoding: UTF-8');

            // $response->setHeaders(Headers::fromString("Content-Type: application/octet-stream\r\nContent-Length: 9\r\nContent-Disposition: attachment; filename=\"blamoo.txt\""));
            $response->setHeaders($headers);
            // $output = fread($fh, 8192);

            $response->setContent($output);

            fclose($fh);
            // unlink($fileName);
            return $response;
        }

        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 15;
        } else {
            $resultsPerPage = $this->params()->fromQuery('perPage');
        }

        if (is_null($this->params()->fromQuery('page'))) {
            $page = 1;
        } else {
            $page = $this->params()->fromQuery('page');
        }
        $totalResults = $this->sparePartCategoryMemberTable->getTotalSP();

        $paginator = null;
        if ($totalResults > $resultsPerPage) {
            $paginator = new Paginator($totalResults, $page, $resultsPerPage);
            $spareparts = $this->sparePartCategoryMemberTable->getAllSP(($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1);
        } else {
            $spareparts = $this->sparePartCategoryMemberTable->getAllSP(0, 0);
        }

        return new ViewModel(array(
            'total_spareparts' => $totalResults,
            'spareparts' => $spareparts,
            'paginator' => $paginator
        ));
    }

    /**
     * List all spare parts
     */
    public function suggestAction()
    {
        $output = $this->params()->fromQuery('output');

        if ($output === 'csv') {

            $fh = fopen('php://memory', 'w');
            // $myfile = fopen('ouptut.csv', 'a+');

            $h = array();
            $h[] = "CAGEGORY";
            $h[] = "TAG";
            $h[] = "NAME";
            $h[] = "CODE";
            $h[] = "LOCATION";
            $h[] = "TOTAL IN";
            $h[] = "TOTAL OUT";
            $h[] = "CURRENT BALANCE";
            $h[] = "MIN. BALANCE";

            $delimiter = ";";

            fputcsv($fh, $h, $delimiter, '"');
            // fputs($fh, implode($h, ',')."\n");

            $spareparts = $this->sparePartCategoryMemberTable->getAllSP(0, 0);

            foreach ($spareparts as $m) {
                $l = array();
                $l[] = (string) $m->cat_name;
                $l[] = (string) '"' . $m->tag . '"';

                $name = (string) $m->name;

                $name === '' ? $name = "-" : $name;

                $name = str_replace(',', '', $name);
                $name = str_replace(';', '', $name);

                $l[] = $name;
                $l[] = (string) $m->code;
                $l[] = (string) $m->location;
                $l[] = (string) $m->total_inflow;
                $l[] = (string) $m->total_outflow;
                $l[] = (string) $m->current_balance;
                $l[] = (string) $m->minimum_balance;

                fputcsv($fh, $l, $delimiter, '"');
                // fputs($fh, implode($l, ',')."\n");
            }

            $fileName = 'spareparts-' . date("m-d-Y") . '-' . date("h:i:sa") . '.csv';
            fseek($fh, 0);
            $output = stream_get_contents($fh);
            // file_put_contents($fileName, $output);

            $response = $this->getResponse();
            $headers = new Headers();

            $headers->addHeaderLine('Content-Type: text/csv');
            // $headers->addHeaderLine ( 'Content-Type: application/vnd.ms-excel; charset=UTF-8' );

            $headers->addHeaderLine('Content-Disposition: attachment; filename="' . $fileName . '"');
            $headers->addHeaderLine('Content-Description: File Transfer');
            $headers->addHeaderLine('Content-Transfer-Encoding: binary');
            $headers->addHeaderLine('Content-Encoding: UTF-8');

            // $response->setHeaders(Headers::fromString("Content-Type: application/octet-stream\r\nContent-Length: 9\r\nContent-Disposition: attachment; filename=\"blamoo.txt\""));
            $response->setHeaders($headers);
            // $output = fread($fh, 8192);

            $response->setContent($output);

            fclose($fh);
            // unlink($fileName);
            return $response;
        }

        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 15;
        } else {
            $resultsPerPage = $this->params()->fromQuery('perPage');
        }

        if (is_null($this->params()->fromQuery('page'))) {
            $page = 1;
        } else {
            $page = $this->params()->fromQuery('page');
        }
        $totalResults = $this->sparePartCategoryMemberTable->getTotalSPHavingMinBalance();

        $paginator = null;
        if ($totalResults > $resultsPerPage) {
            $paginator = new Paginator($totalResults, $page, $resultsPerPage);
            $spareparts = $this->sparePartCategoryMemberTable->getOrderSuggestion(($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1);
        } else {
            $spareparts = $this->sparePartCategoryMemberTable->getOrderSuggestion(0, 0);
        }

        return new ViewModel(array(
            'total_spareparts' => $totalResults,
            'spareparts' => $spareparts,
            'paginator' => $paginator
        ));
    }

    /**
     * List all spare parts
     */
    public function list1Action()
    {
        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 20;
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

        $spareparts = $this->sparePartTable->fetchAll();
        $totalResults = $spareparts->count();

        $paginator = null;
        if ($totalResults > $resultsPerPage) {
            $paginator = new Paginator($totalResults, $page, $resultsPerPage);
            $spareparts = $this->sparePartTable->getLimitSpareParts(($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1);
        }

        $this->layout('layout/no-layout');

        return new ViewModel(array(
            'total_spareparts' => $totalResults,
            'spareparts' => $spareparts,
            'paginator' => $paginator
        ));
    }

    /*
     * *
     */
    public function categoryAction()
    {
        $categories = $this->sparePartCategoryTable->getCategories1();

        return new ViewModel(array(
            'assetCategories' => $categories
        ));
    }

    /*
     * *
     */
    public function category1Action()
    {
        $categories = $this->sparePartCategoryTable->getCategories1();
        $this->layout('layout/no-layout');

        $viewModel = new ViewModel(array(
            'assetCategories' => $categories
        ));

        return $viewModel;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function showCategoryAction()
    {
        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 15;
        } else {
            $resultsPerPage = $this->params()->fromQuery('perPage');
        }

        if (is_null($this->params()->fromQuery('page'))) {
            $page = 1;
        } else {
            $page = $this->params()->fromQuery('page');
        }

        $id = $this->params()->fromQuery('id');

        $category = $this->sparePartCategoryTable->get($id);
        $totalResults = $this->sparePartCategoryMemberTable->getTotalMembersOfCatID($id);

        $paginator = null;
        if ($totalResults > $resultsPerPage) {
            $paginator = new Paginator($totalResults, $page, $resultsPerPage);
            $spareparts = $this->sparePartCategoryMemberTable->getMembersOfCatID($id, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1);
        } else {
            $spareparts = $this->sparePartCategoryMemberTable->getMembersOfCatID($id, 0, 0);
        }

        return new ViewModel(array(
            'category' => $category,
            'total_spareparts' => $totalResults,
            'spareparts' => $spareparts,
            'paginator' => $paginator
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function showCategoryAlbumAction()
    {
        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 15;
        } else {
            $resultsPerPage = $this->params()->fromQuery('perPage');
        }

        if (is_null($this->params()->fromQuery('page'))) {
            $page = 1;
        } else {
            $page = $this->params()->fromQuery('page');
        }

        $id = $this->params()->fromQuery('id');

        $category = $this->sparePartCategoryTable->get($id);
        $totalResults = $this->sparePartCategoryMemberTable->getTotalMembersOfCatID($id);

        $paginator = null;
        /*
         * if ($totalResults > $resultsPerPage) {
         * $paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
         * //$spareparts = $this->sparePartCategoryMemberTable->getMembersOfCatID ( $id, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1 );
         * $spareparts = $this->sparePartCategoryMemberTable->getMembersOfCatID ( $id, 0, 0 );
         * } else {
         * $spareparts = $this->sparePartCategoryMemberTable->getMembersOfCatID ( $id, 0, 0 );
         * }
         */
        $spareparts = $this->sparePartCategoryMemberTable->getMembersOfCatID($id, 0, 0);
        return new ViewModel(array(
            'category' => $category,
            'total_spareparts' => $totalResults,
            'spareparts' => $spareparts,
            'paginator' => null
        ));
    }

    /**
     * Show detail of a spare parts
     */
    public function showAction()
    {
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        $id = (int) $this->params()->fromQuery('id');
        $sp = $this->sparePartTable->get($id);
        $pictures = $this->sparePartPictureTable->getSparepartPicturesById($id);
        $inflow = $this->sparepartMovementsTable->getTotalInflowOf($id);
        $outflow = $this->sparepartMovementsTable->getTotalOutflowOf($id);
        $categories = $this->sparePartCategoryTable->getCategoriesOf($id);

        $instock = $inflow - $outflow;

        return new ViewModel(array(
            'sparepart' => $sp,
            'pictures' => $pictures,
            'inflow' => $inflow,
            'outflow' => $outflow,
            'instock' => $instock,
            'categories' => $categories,
            'redirectUrl' => $redirectUrl
        ));
    }

    /**
     * Show detail of a spare parts
     */
    public function showPicturesAction()
    {
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        $id = (int) $this->params()->fromQuery('id');
        $sp = $this->sparePartTable->get($id);
        $pictures = $this->sparePartPictureTable->getSPPicturesById($id);

        return new ViewModel(array(
            'sparepart' => $sp,
            'pictures' => $pictures,
            'redirectUrl' => $redirectUrl
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function picturesAction()
    {
        $id = (int) $this->params()->fromQuery('sparepart_id');
        $sp = $this->sparePartTable->get($id);
        $pictures = $this->sparePartPictureTable->getSparepartPicturesById($id);

        return new ViewModel(array(
            'sparepart' => $sp,
            'pictures' => $pictures
        ));
    }

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
     */
    public function showMovementAction()
    {
        // $redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
        $id = (int) $this->params()->fromQuery('sparepart_id');
        $fromDate = $this->params()->fromQuery('start_date');
        $toDate = $this->params()->fromQuery('end_date');
        $flow = $this->params()->fromQuery('flow');
        $output = $this->params()->fromQuery('output');
        $layout = $this->params()->fromQuery('layout');

        $movements = $this->sparepartMovementsTable->getSparePartMovementsByID($id, $fromDate, $toDate, $flow, 0, 0);

        if ($output === 'csv') {

            $fh = fopen('php://memory', 'w');
            // $myfile = fopen('ouptut.csv', 'a+');

            $h = array();
            $h[] = "DATE";
            $h[] = "TAG";
            $h[] = "NAME";
            $h[] = "QUANTITY";
            $h[] = "FLOW";

            $delimiter = ";";

            fputcsv($fh, $h, $delimiter, '"');
            // fputs($fh, implode($h, ',')."\n");

            foreach ($movements as $m) {
                $l = array();
                $l[] = (string) $m->movement_date;
                $l[] = (string) '"' . $m->tag . '"';

                $name = (string) $m->sparepart_name;

                $name === '' ? $name = "-" : $name;

                $name = str_replace(',', '', $name);
                $name = str_replace(';', '', $name);

                $l[] = $name;
                $l[] = (string) $m->quantity;
                $l[] = (string) $m->flow;

                fputcsv($fh, $l, $delimiter, '"');
                // fputs($fh, implode($l, ',')."\n");
            }

            $fileName = 'report.csv';
            fseek($fh, 0);
            $output = stream_get_contents($fh);
            // file_put_contents($fileName, $output);

            $response = $this->getResponse();
            $headers = $response->getHeaders();

            $headers->addHeaderLine('Content-Type: text/csv');

            $headers->addHeaderLine('Content-Disposition: attachment; "' . $fileName . '"');
            $headers->addHeaderLine('Content-Description: File Transfer');
            $headers->addHeaderLine('Content-Transfer-Encoding: binary');
            $headers->addHeaderLine('Content-Encoding: UTF-8');

            // $output = fread($fh, 8192);
            $response->setContent($output);

            fclose($fh);
            // unlink($fileName);

            return $response;
        } else {

            if (is_null($this->params()->fromQuery('perPage'))) {
                $resultsPerPage = 18;
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

            $totalResults = count($movements);

            $paginator = null;
            if ($totalResults > $resultsPerPage) {
                $paginator = new Paginator($totalResults, $page, $resultsPerPage);
                $movements = $this->sparepartMovementsTable->getSparePartMovementsByID($id, $fromDate, $toDate, $flow, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1);
            }

            if ($layout == "ajax") {
                $this->layout("layout/inventory/ajax");
            }

            return new ViewModel(array(
                'movements' => $movements,
                // 'redirectUrl' => $redirectUrl,
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'flow' => $flow,
                'paginator' => $paginator,
                'total_items' => $totalResults,
                'id' => $id
            ));
        }
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function setMinBalanceAction()
    {
        $request = $this->getRequest();
        $identity = $this->authService->getIdentity();
        $user = $this->userTable->getUserByEmail($identity);

        if ($request->isPost()) {

            if ($request->isPost()) {
                $redirectUrl = $request->getPost('redirectUrl');

                $input = new SparepartMinimumBalance();
                $input->sparepart_id = (int) $request->getPost('sparepart_id');
                $input->minimum_balance = $request->getPost('minimum_balance');
                $input->remarks = $request->getPost('remarks');
                $input->status = $request->getPost('status');
                $input->created_by = $user["id"];

                $errors = array();

                $validator = new Int();

                if (! $validator->isValid($input->minimum_balance)) {
                    $errors[] = 'Quantity is not valid. It must be a number.';
                }

                if (count($errors) > 0) {

                    $sp = $this->spMinimumBalanceTable->getSparepartID($input->sparepart_id);
                    $pictures = $this->sparePartPictureTable->getSparepartPicturesById($input->sparepart_id);

                    return new ViewModel(array(
                        'errors' => $errors,
                        'sparepart' => $sp,
                        'pictures' => $pictures,
                        'redirectUrl' => $redirectUrl
                    ));
                }

                $this->spMinimumBalanceTable->add($input);
                $this->redirect()->toUrl($redirectUrl);
            }
        }

        $id = (int) $this->params()->fromQuery('sparepart_id');
        $sp = $this->spMinimumBalanceTable->getSparepartID($id);
        $pictures = $this->sparePartPictureTable->getSparepartPicturesById($id);

        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        return new ViewModel(array(
            'sparepart' => $sp,
            'pictures' => $pictures,
            'errors' => null,
            'redirectUrl' => $redirectUrl
        ));
    }

    // SETTER AND GETTER
    public function getAuthService()
    {
        return $this->authService;
    }

    public function setAuthService($authService)
    {
        $this->authService = $authService;
        return $this;
    }

    public function setSmtpTransportService($SmtpTransportService)
    {
        $this->SmtpTransportService = $SmtpTransportService;
        return $this;
    }

    public function setSparePartService(SparepartService $sparePartService)
    {
        $this->sparePartService = $sparePartService;
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

    public function getSparePartTable()
    {
        return $this->sparePartTable;
    }

    public function setSparePartTable(MLASparepartTable $sparePartTable)
    {
        $this->sparePartTable = $sparePartTable;
        return $this;
    }

    public function setSparepartMovementsTable(SparepartMovementsTable $sparepartMovementsTable)
    {
        $this->sparepartMovementsTable = $sparepartMovementsTable;
        return $this;
    }

    public function setSparePartCategoryTable(SparepartCategoryTable $sparePartCategoryTable)
    {
        $this->sparePartCategoryTable = $sparePartCategoryTable;
        return $this;
    }

    public function setSparePartPictureTable(SparepartPictureTable $sparePartPictureTable)
    {
        $this->sparePartPictureTable = $sparePartPictureTable;
        return $this;
    }

    public function setSparePartCategoryMemberTable(SparepartCategoryMemberTable $sparePartCategoryMemberTable)
    {
        $this->sparePartCategoryMemberTable = $sparePartCategoryMemberTable;
        return $this;
    }

    public function getSparePartService()
    {
        return $this->sparePartService;
    }

    public function getSmtpTransportService()
    {
        return $this->SmtpTransportService;
    }

    public function getSparePartPictureTable()
    {
        return $this->sparePartPictureTable;
    }

    public function getSparepartMovementsTable()
    {
        return $this->sparepartMovementsTable;
    }

    public function getSparePartCategoryTable()
    {
        return $this->sparePartCategoryTable;
    }

    public function getSparePartCategoryMemberTable()
    {
        return $this->sparePartCategoryMemberTable;
    }

    public function getPurchaseRequestCartItemTable()
    {
        return $this->purchaseRequestCartItemTable;
    }

    public function setPurchaseRequestCartItemTable(PurchaseRequestCartItemTable $purchaseRequestCartItemTable)
    {
        $this->purchaseRequestCartItemTable = $purchaseRequestCartItemTable;
        return $this;
    }

    public function getSpMinimumBalanceTable()
    {
        return $this->spMinimumBalanceTable;
    }

    public function setSpMinimumBalanceTable(SparepartMinimumBalanceTable $spMinimumBalanceTable)
    {
        $this->spMinimumBalanceTable = $spMinimumBalanceTable;
        return $this;
    }

    public function getPrItemTable()
    {
        return $this->prItemTable;
    }

    public function setPrItemTable(PurchaseRequestItemTable $prItemTable)
    {
        $this->prItemTable = $prItemTable;
        return $this;
    }
}
