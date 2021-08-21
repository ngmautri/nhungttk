<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace HR\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use Application\Domain\Util\Pagination\Paginator;
use Application\Entity\NmtApplicationAttachment;
use Zend\Http\Headers;
use Zend\Validator\Date;
use Zend\Math\Rand;

/**
 *
 * @author nmt
 *        
 */
class EmployeeOTController extends AbstractActionController
{

    const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $doctrineEM;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

    /**
     */
    public function addAction()
    {
        $redirectUrl = null;
        $target = null;
        $entity = null;

        $id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        // Target: Employee
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findOneBy($criteria);

        if ($target !== null) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'target' => $target,
                'entity' => $entity
            ));
        }
        return $this->redirect()->toRoute('access_denied');
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function showAction()
    {
        $request = $this->getRequest();
        $redirectUrl = null;

        if ($request->getHeader('Referer') == null) {
            // return $this->redirect ()->toRoute ( 'access_denied' );
        } else {
            $redirectUrl = $request->getHeader('Referer')->getUri();
        }

        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $entity_id,
            'checksum' => $checksum,
            'token' => $token
        );

        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findOneBy($criteria);

        if (! $entity == null) {

            /**
             *
             * @todo Update Target
             */
            $target = $entity->getEmployee();

            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'target' => $target,
                'entity' => $entity
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));

            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            $entity_id = (int) $request->getPost('entity_id');
            $token = $request->getPost('token');

            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );

            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findOneBy($criteria);

            if ($entity == null) {

                $errors[] = 'Entity object can\'t be empty!';
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'target' => null,
                    'entity' => null
                ));

                // might need redirect
            } else {

                $vendor_id = $request->getPost('vendor_id');
                $documentSubject = $request->getPost('documentSubject');
                $validFrom = $request->getPost('validFrom');
                $validTo = $request->getPost('validTo');
                $isActive = $request->getPost('isActive');
                $markedForDeletion = $request->getPost('markedForDeletion');
                $filePassword = $request->getPost('filePassword');
                $visibility = $request->getPost('visibility');
                $filePassword = $request->getPost('filePassword');

                /**
                 *
                 * @todo : Change Target
                 */
                $target = $entity->getEmployee();

                // to Add
                $target_id = null;
                if ($target !== null) {
                    $target_id = $target->getId();
                }

                // to Comment
                // $entity = new NmtApplicationAttachment ();

                $remarks = $request->getPost('remarks');

                if ($documentSubject == null) {
                    $errors[] = 'Please give document subject!';
                } else {
                    $entity->setDocumentSubject($documentSubject);
                }

                if ($isActive != 1) {
                    $isActive = 0;
                }

                if ($markedForDeletion != 1) {
                    $markedForDeletion = 0;
                }

                if ($visibility != 1) {
                    $visibility = 0;
                }

                $entity->setFilePassword($filePassword);

                $entity->setIsActive($isActive);
                $entity->setMarkedForDeletion($markedForDeletion);
                $entity->setVisibility($visibility);

                if ($filePassword === null or $filePassword == "") {
                    $filePassword = self::PDF_PASSWORD;
                }

                // validator.
                $validator = new Date();
                $date_to_validate = 2;
                $date_validated = 0;

                // EMPTY is ok
                if ($validFrom !== null) {
                    if ($validFrom !== "") {
                        if (! $validator->isValid($validFrom)) {
                            $errors[] = 'Start date is not correct or empty!';
                        } else {
                            $date_validated ++;
                            $entity->setValidFrom(new \DateTime($validFrom));
                        }
                    }
                }

                // EMPTY is ok
                if ($validTo !== null) {
                    if ($validTo !== "") {

                        if (! $validator->isValid($validTo)) {
                            $errors[] = 'End date is not correct or empty!';
                        } else {
                            $date_validated ++;
                            $entity->setValidTo(new \DateTime($validTo));
                        }
                    }
                }

                // all date corrected
                if ($date_validated == $date_to_validate) {

                    if ($validFrom > $validTo) {
                        $errors[] = 'End date must be in future!';
                    }
                }

                $entity->setRemarks($remarks);

                $vendor = null;
                if ($vendor_id > 0) {
                    $vendor = $this->doctrineEM->find('Application\Entity\NmtBpVendor', $vendor_id);
                    // $entity->setVendor ( $vendor );
                }

                $entity->setVendor($vendor);

                // handle attachment
                if (isset($_FILES['attachments'])) {
                    $file_name = $_FILES['attachments']['name'];
                    $file_size = $_FILES['attachments']['size'];
                    $file_tmp = $_FILES['attachments']['tmp_name'];
                    $file_type = $_FILES['attachments']['type'];
                    $file_ext = strtolower(end(explode('.', $_FILES['attachments']['name'])));

                    // attachement required?
                    if ($file_tmp == "" or $file_tmp === null) {

                        // $errors [] = 'Attachment can\'t be empby!';
                        if (count($errors) > 0) {

                            $this->flashMessenger()->addMessage('Something wrong!');
                            return new ViewModel(array(
                                'redirectUrl' => $redirectUrl,
                                'errors' => $errors,
                                'target' => $target,
                                'entity' => $entity
                            ));
                        }

                        $entity->setLastChangeBy($u);
                        $entity->setLastChangeOn(new \DateTime());

                        // update last change, without Attachment
                        $this->doctrineEM->flush();
                        $this->flashMessenger()->addMessage('Attachment "' . $entity_id . '" has been updated. File is not changed!');
                        return $this->redirect()->toUrl($redirectUrl);
                    } else {

                        $ext = '';
                        $isPicture = 0;
                        if (preg_match('/(jpg|jpeg)$/', $file_type)) {
                            $ext = 'jpg';
                            $isPicture = 1;
                        } else if (preg_match('/(gif)$/', $file_type)) {
                            $ext = 'gif';
                            $isPicture = 1;
                        } else if (preg_match('/(png)$/', $file_type)) {
                            $ext = 'png';
                            $isPicture = 1;
                        } else if (preg_match('/(pdf)$/', $file_type)) {
                            $ext = 'pdf';
                        } else if (preg_match('/(vnd.ms-excel)$/', $file_type)) {
                            $ext = 'xls';
                        } else if (preg_match('/(vnd.openxmlformats-officedocument.spreadsheetml.sheet)$/', $file_type)) {
                            $ext = 'xlsx';
                        } else if (preg_match('/(msword)$/', $file_type)) {
                            $ext = 'doc';
                        } else if (preg_match('/(vnd.openxmlformats-officedocument.wordprocessingml.document)$/', $file_type)) {
                            $ext = 'docx';
                        } else if (preg_match('/(x-zip-compressed)$/', $file_type)) {
                            $ext = 'zip';
                        } else if (preg_match('/(octet-stream)$/', $file_type)) {
                            $ext = $file_ext;
                        }

                        $expensions = array(
                            "jpeg",
                            "jpg",
                            "png",
                            "pdf",
                            "xlsx",
                            "xls",
                            "docx",
                            "doc",
                            "zip",
                            "msg"
                        );

                        if (in_array($ext, $expensions) === false) {
                            $errors[] = 'Extension file"' . $ext . '" not supported, please choose a "jpeg","jpg","png","pdf","xlsx","xlx", "docx"!';
                        }

                        if ($file_size > 10485760) {
                            $errors[] = 'File size must be excately 2 MB';
                        }

                        $checksum = md5_file($file_tmp);

                        /**
                         *
                         * @todo : Change Target
                         */
                        $criteria = array(
                            "checksum" => $checksum,
                            "employee" => $target_id
                        );
                        $ck = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findby($criteria);

                        if (count($ck) > 0) {
                            $errors[] = 'Document: "' . $file_name . '"  exits already';
                        }

                        if (count($errors) > 0) {
                            $this->flashMessenger()->addMessage('Something wrong!');
                            return new ViewModel(array(
                                'redirectUrl' => $redirectUrl,
                                'errors' => $errors,
                                'target' => $target,
                                'entity' => $entity
                            ));
                        }
                        ;

                        // deactive current
                        $entity->setIsactive(0);
                        $entity->setMarkedForDeletion(1);
                        $entity->setLastChangeBy($u);
                        $entity->setLastChangeOn(new \DateTime());

                        $name_part1 = Rand::getString(6, self::CHAR_LIST, true) . "_" . Rand::getString(10, self::CHAR_LIST, true);
                        $name = md5($target_id . $checksum . uniqid(microtime())) . '_' . $name_part1 . '.' . $ext;

                        $folder_relative = $name[0] . $name[1] . DIRECTORY_SEPARATOR . $name[2] . $name[3] . DIRECTORY_SEPARATOR . $name[4] . $name[5];
                        $folder = ROOT . self::ATTACHMENT_FOLDER . DIRECTORY_SEPARATOR . $folder_relative;

                        if (! is_dir($folder)) {
                            mkdir($folder, 0777, true); // important
                        }

                        move_uploaded_file($file_tmp, "$folder/$name");

                        if ($ext == "pdf") {
                            $pdf_box = ROOT . self::PDFBOX_FOLDER;

                            // java -jar pdfbox-app-2.0.5.jar Encrypt [OPTIONS] <password> <inputfile>
                            exec('java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar Encrypt -O mla2017 -U ' . $filePassword . ' ' . "$folder/$name");

                            // extract text:
                            exec('java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar ExtractText -password ' . $filePassword . ' ' . "$folder/$name" . ' ' . "$folder/$name" . '.txt');
                        }

                        // if new attachment upload, then clone new one
                        $cloned_entity = clone $entity;

                        // copy new one
                        $cloned_entity->setIsactive(1);
                        $cloned_entity->setMarkedForDeletion(0);
                        $cloned_entity->setChangeFor($entity->getId());

                        $cloned_entity->setFilePassword($filePassword);
                        $cloned_entity->setIsPicture($isPicture);
                        $cloned_entity->setFilename($name);
                        $cloned_entity->setFiletype($file_type);
                        $cloned_entity->setFilenameOriginal($file_name);
                        $cloned_entity->setSize($file_size);
                        $cloned_entity->setFolder($folder);

                        // new
                        $cloned_entity->setAttachmentFolder(self::ATTACHMENT_FOLDER . DIRECTORY_SEPARATOR . $folder_relative . DIRECTORY_SEPARATOR);
                        $cloned_entity->setFolderRelative($folder_relative . DIRECTORY_SEPARATOR);
                        $cloned_entity->setChecksum($checksum);
                        $cloned_entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                        $cloned_entity->setCreatedBy($u);
                        $cloned_entity->setCreatedOn(new \DateTime());
                        $this->doctrineEM->persist($cloned_entity);
                        $this->doctrineEM->flush();

                        $this->flashMessenger()->addMessage('Attachment "' . $entity_id . '" has been updated with new file uploaded');
                        return $this->redirect()->toUrl($redirectUrl);
                    }
                }
            }
        }

        $redirectUrl = null;

        if ($request->getHeader('Referer') == null) {
            // return $this->redirect ()->toRoute ( 'access_denied' );
        } else {
            $redirectUrl = $request->getHeader('Referer')->getUri();
        }

        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $entity_id,
            'checksum' => $checksum,
            'token' => $token
        );

        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findOneBy($criteria);

        if (! $entity == null) {

            /**
             *
             * @todo : Update Target
             */
            $target = $entity->getEmployee();

            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'target' => $target,
                'entity' => $entity
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
    {}

    /**
     * Return attachment of a target
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function list1Action()
    {
        $request = $this->getRequest();

        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        ;

        $this->layout("layout/user/ajax");

        $target_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $target_id,
            'token' => $token
        );

        /**
         *
         * @todo : Change Target
         */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findOneBy($criteria);

        if ($target !== null) {

            /**
             *
             * @todo : Change Target
             */
            $criteria = array(
                'employee' => $target_id,
                'isActive' => 1,
                'markedForDeletion' => 0
            );

            $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findBy($criteria);
            $total_records = count($list);
            $paginator = null;

            return new ViewModel(array(
                'list' => $list,
                'total_records' => $total_records,
                'paginator' => $paginator,
                'target' => $target
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function updateTokenAction()
    {

        /**
         *
         * @todo : update target
         */
        $query = 'SELECT e FROM Application\Entity\NmtApplicationAttachment e WHERE e.employee > :n';

        $list = $this->doctrineEM->createQuery($query)
            ->setParameter('n', 0)
            ->getResult();

        if (count($list) > 0) {
            foreach ($list as $entity) {
                /**
                 *
                 * @todo Update Targnet
                 */
                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            }
        }

        $this->doctrineEM->flush();

        $total_records = count($list);
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
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
