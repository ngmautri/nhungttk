<?php
namespace Application\Controller;

use Application\Entity\NmtApplicationCompany;
use Application\Entity\NmtApplicationCompanyLogo;
use Application\Entity\NmtApplicationCompanyMember;
use Application\Listener\PictureUploadListener;
use Application\Service\DepartmentService;
use Doctrine\ORM\EntityManager;
use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CompanyController extends AbstractActionController
{

    const CHAR_LIST = "__0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ__";

    protected $doctrineEM;

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $identity = $this->authService->getIdentity();
        $user = $this->userTable->getUserByEmail($identity);
        $u = $this->doctrineEM->find('Application\Entity\MlaUsers', $user['id']);

        if ($request->isPost()) {

            // $input->status = $request->getPost ( 'status' );
            // $input->remarks = $request->getPost ( 'description' );

            $company_code = $request->getPost('company_code');
            $company_name = $request->getPost('company_name');
            $status = $request->getPost('status');

            $errors = array();

            if ($company_name === '' or $company_name === null) {
                $errors[] = 'Please give the name';
            }

            if ($company_code === '' or $company_code === null) {
                $errors[] = 'Please give the code';
            }

            $r = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCompany')->findBy(array(
                'companyName' => $company_name
            ));

            if (count($r) >= 1) {
                $errors[] = $company_name . ' exists';
            }

            $r = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCompany')->findBy(array(
                'companyCode' => $company_code
            ));

            if (count($r) >= 1) {
                $errors[] = $company_code . ' exists';
            }

            if (count($errors) > 0) {
                return new ViewModel(array(
                    'errors' => $errors
                ));
            }

            // NO ERROR
            // ++++++++++++++++++++++++++++++++++

            $entity = new NmtApplicationCompany();
            $entity->setCompanyCode($company_code);
            $entity->setCompanyName($company_name);
            $entity->setCreatedOn(new \DateTime());
            $entity->setCreatedBy($u);
            $entity->setStatus($status);
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
        }

        // NO POST
        // =========================
        return new ViewModel(array(
            'errors' => null
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();

        $request = $this->getRequest();

        if ($request->isPost()) {

            $errors = array();

            $redirectUrl = $request->getPost('redirectUrl');
            $entity_id = (int) $request->getPost('entity_id');
            $token = $request->getPost('token');
            $nTry = $request->getPost('n');

            if ($token == "") {
                $token = null;
            }

            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );

            /**@var \Application\Entity\NmtApplicationCompany $entity ;*/
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCompany')->findOneBy($criteria);

            if (! $entity instanceof \Application\Entity\NmtApplicationCompany) {
                $errors[] = "Entity not found!";

                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'entity' => $entity,
                    'errors' => $errors,
                    'n' => $nTry,
                    'currency_list' => $currency_list
                ));
            }

            $oldEntity = clone ($entity);

            $currency_id = (int) $request->getPost('currency_id');

            $currency = null;
            if ($currency_id > 0) {
                /** @var \Application\Entity\NmtApplicationCurrency  $currency ; */
                $currency = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($currency_id);
            }

            if ($currency !== null) {
                $entity->setDefaultCurrency($currency);
            } else {
                $errors[] = 'Currency can\'t be empty. Please select a currency!';
            }

            $changeArray = $nmtPlugin->objectsAreIdentical($oldEntity, $entity);

            if (count($changeArray) == 0) {
                $nTry ++;
                $errors[] = sprintf('Nothing changed! n = %s', $nTry);
            }

            if ($nTry >= 3) {
                $errors[] = sprintf('Do you really want to edit "%s (%s)"?', $entity->getCompanyName(), $entity->getCompanyCode());
            }

            if ($nTry == 5) {
                $m = sprintf('You might be not ready to edit "%s (%s)". Please try later!', $entity->getCompanyName(), $entity->getCompanyCode());
                $this->flashMessenger()->addMessage($m);
                return $this->redirect()->toUrl($redirectUrl);
            }

            if (count($errors) > 0) {

                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'entity' => $entity,
                    'errors' => $errors,
                    'n' => $nTry,
                    'currency_list' => $currency_list
                ));
            }

            // NO ERROR
            // ++++++++++++++++++++++++++++++++++

            if ($entity->getToken() == null) {
                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            }

            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();

            $m = sprintf('Company %s - %s updated. OK!', $entity->getCompanyName(), $entity->getCompanyCode());
            $this->flashMessenger()->addMessage($m);
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // ===========================

        $redirectUrl = null;
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');

        if ($token == "") {
            $token = null;
        }

        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCompany')->findOneBy($criteria);
        if ($entity instanceof \Application\Entity\NmtApplicationCompany) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'n' => 0,
                'currency_list' => $currency_list
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function showAction()
    {

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();

        $request = $this->getRequest();

        $redirectUrl = null;
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');

        if ($token == "") {
            $token = null;
        }

        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCompany')->findOneBy($criteria);
        if ($entity instanceof \Application\Entity\NmtApplicationCompany) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'n' => 0,
                'currency_list' => $currency_list
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCompany')->findAll();
        $total_records = count($list);
        // $jsTree = $this->tree;
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => null
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function addMemberAction()
    {
        $request = $this->getRequest();
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        $identity = $this->authService->getIdentity();
        $user = $this->userTable->getUserByEmail($identity);
        $u = $this->doctrineEM->find('Application\Entity\MlaUsers', $user['id']);

        if ($request->isPost()) {

            $company_id = (int) $request->getPost('company_id');
            $user_id_list = $request->getPost('users');

            if (count($user_id_list) > 0) {
                foreach ($user_id_list as $member_id) {

                    $criteria = array(
                        'company' => $company_id,
                        'user' => $member_id
                    );

                    $isMember = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCompanyMember')->findBy($criteria);

                    if (count($isMember) == 0) {
                        $member = new NmtApplicationCompanyMember();
                        $role = $this->doctrineEM->find('Application\Entity\NmtApplicationCompany', $company_id);
                        $member->setCompany($role);
                        $m = $this->doctrineEM->find('Application\Entity\MlaUsers', $member_id);
                        $member->setUser($m);
                        $member->setCreatedBy($u);
                        $member->setCreatedOn(new \DateTime());
                        $this->doctrineEM->persist($member);
                        $this->doctrineEM->flush();
                    }
                }

                $redirectUrl = $request->getPost('redirectUrl');
                $this->redirect()->toUrl($redirectUrl);
            }
        }

        $company_id = (int) $this->params()->fromQuery('company_id');
        // $role = $this->aclRoleTable->getRole ( $id );
        $company = $this->doctrineEM->find('Application\Entity\NmtApplicationCompany', $company_id);
        // var_dump($company_id);
        // No Doctrine
        $users = $this->userTable->getNoneMembersOfCompany($company_id);

        return new ViewModel(array(
            'company' => $company,
            'users' => $users,
            'redirectUrl' => $redirectUrl
        ));
    }

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
     */
    public function uploadLogoAction()
    {
        $request = $this->getRequest();
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        $user = $this->userTable->getUserByEmail($this->identity());
        $u = $this->doctrineEM->find('Application\Entity\MlaUsers', $user['id']);

        if ($request->isPost()) {

            $pictures = $_POST['pictures'];
            $id = $_POST['target_id'];

            $result = "";

            foreach ($pictures as $p) {
                $filetype = $p[0];
                $result = $result . $p[2];
                $original_filename = $p[2];

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

                // $root_dir = $this->articleService->getPicturesPath ();
                $root_dir = ROOT . "/data/application/picture/company/";

                $criteria = array(
                    "checksum" => $checksum,
                    "company" => $id
                );

                $ck = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCompanyLogo')->findby($criteria);

                if (count($ck) == 0) {
                    $name = md5($id . $checksum . uniqid(microtime())) . '.' . $ext;
                    $folder = $root_dir . DIRECTORY_SEPARATOR . $name[0] . $name[1] . DIRECTORY_SEPARATOR . $name[2] . $name[3] . DIRECTORY_SEPARATOR . $name[4] . $name[5];

                    if (! is_dir($folder)) {
                        mkdir($folder, 0777, true); // important
                    }

                    rename($tmp_name, "$folder/$name");

                    $entity = new NmtApplicationCompanyLogo();
                    $entity->setUrl($folder . DIRECTORY_SEPARATOR . $name);
                    $entity->setFiletype($filetype);
                    $entity->setFilename($name);
                    $entity->setOriginalFilename($original_filename);
                    $entity->setFolder($folder);
                    $entity->setChecksum($checksum);
                    $company = $this->doctrineEM->find('Application\Entity\NmtApplicationCompany', $id);
                    $entity->setCompany($company);
                    $entity->setCreatedBy($u);
                    $entity->setCreatedOn(new \DateTime());

                    $this->doctrineEM->persist($entity);
                    $this->doctrineEM->flush();

                    // trigger uploadPicture. AbtractController is EventManagerAware.
                    $this->getEventManager()->trigger('uploadPicture', __CLASS__, array(
                        'picture_name' => $name,
                        'pictures_dir' => $folder
                    ));

                    $result = $result . ' uploaded. //';
                } else {
                    $result = $result . ' exits. //';
                }
            }
            // $data['filetype'] = $filetype;
            $data = array();
            $data['message'] = $result;
            $response = $this->getResponse();
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            return $response;
        }

        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        $id = (int) $this->params()->fromQuery('target_id');
        // $company = $this->articleTable->get ( $id );
        // $company = $this->doctrineEM->find('Application\Entity\NmtApplicationCompanyLogo',$id);
        $company = $this->doctrineEM->find('Application\Entity\NmtApplicationCompany', $id);

        return new ViewModel(array(
            'company' => $company,
            'redirectUrl' => $redirectUrl,
            'errors' => null
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
}
