<?php
namespace Procure\Controller;

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
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class QuoteAttachmentController extends AbstractActionController
{

    /**
     *
     * @todo : TO UPDATE
     */
    const ATTACHMENT_FOLDER = "/data/procure/attachment/quote";

    const PDFBOX_FOLDER = "/vendor/pdfbox/";

    const PDF_PASSWORD = "mla2017";

    const CHAR_LIST = "_0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $doctrineEM;

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
        
        if ($entity instanceof \Application\Entity\NmtApplicationAttachment) {
            
            /**
             *
             * @todo : Change Target
             * @var \Application\Entity\NmtApplicationAttachment $entity ;
             *     
             */
            $target = $entity->getQo();
            
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
            
            /**
             *
             * @var \Application\Entity\NmtApplicationAttachment $entity ;
             */
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findOneBy($criteria);
            
            if (! $entity instanceof \Application\Entity\NmtApplicationAttachment) {
                
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
                $target = $entity->getQo();
                
                // to Add
                $target_id = null;
                if ($target != null) {
                    $target_id = $target->getId();
                }
                
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
                        
                        if ($file_size > 2097152) {
                            $errors[] = 'File size must be excately 2 MB';
                        }
                        
                        $checksum = md5_file($file_tmp);
                        
                        /**
                         *
                         * @todo : Change Target
                         */
                        $criteria = array(
                            "checksum" => $checksum,
                            "qo" => $target_id
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
                        
                        /**
                         * Important! for UBUNTU
                         */
                        $folder = str_replace('\\', '/', $folder);
                        
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
                        
                        $m = sprintf("[OK] %s updated!", $file_name);
                        $this->flashMessenger()->addMessage($m);
                        
                        // Trigger: finance.activity.log. AbtractController is EventManagerAware.
                        $this->getEventManager()->trigger('procure.activity.log', __METHOD__, array(
                            'priority' => \Zend\Log\Logger::INFO,
                            'message' => $m,
                            'createdBy' => $u,
                            'createdOn' => $createdOn,
                            'entity_id' => $entity->getId(),
                            'entity_class' => get_class($entity),
                            'entity_token' => $entity->getToken()
                        ));
                        
                        $this->flashMessenger()->addMessage($m);
                        return $this->redirect()->toUrl($redirectUrl);
                    }
                }
            }
        }
        
        // NO POST
        // Initiate ....
        // ================================
        $redirectUrl = null;
        
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
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
        
        /**@var \Application\Entity\NmtApplicationAttachment $entity*/
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findOneBy($criteria);
        
        if ($entity instanceof \Application\Entity\NmtApplicationAttachment) {
            
            /**
             *
             * @todo : Update Target
             */
            $target = $entity->getQo();
            
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
    {
        // blank
    }

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
         * @var \Application\Entity\NmtProcureQo $target ;
         *     
         */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtProcureQo')->findOneBy($criteria);
        
        if ($target instanceof \Application\Entity\NmtProcureQo) {
            $criteria = array(
                'targetId' => $target_id,
                'targetClass' => get_class($target),
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
    public function getPicturesAction()
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
         * @var \Application\Entity\NmtProcureQo $target ;
         *     
         */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtProcureQo')->findOneBy($criteria);
        
        if ($target instanceof \Application\Entity\NmtProcureQo) {
            
            /**
             *
             * @todo : Update Target
             */
            $criteria = array(
                'qo' => $target_id,
                'isActive' => 1,
                'markedForDeletion' => 0,
                'isPicture' => 1
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
     * @return void|\Zend\Stdlib\ResponseInterface
     */
    public function pictureAction()
    {
        $id = (int) $this->params()->fromQuery('id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        
        if ($token == '') {
            $token = null;
        }
        
        $criteria = array(
            'id' => $id,
            'checksum' => $checksum,
            'token' => $token,
            'markedForDeletion' => 0,
            'isPicture' => 1
        
        );
        
        $pic = new \Application\Entity\NmtApplicationAttachment();
        $pic = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findOneBy($criteria);
        if ($pic !== null) {
            $pic_folder = getcwd() . self::ATTACHMENT_FOLDER . DIRECTORY_SEPARATOR . $pic->getFolderRelative() . $pic->getFileName();
            
            /**
             * Important! for UBUNTU
             */
            $pic_folder = str_replace('\\', '/', $pic_folder);
            
            $imageContent = file_get_contents($pic_folder);
            
            $response = $this->getResponse();
            
            $response->setContent($imageContent);
            $response->getHeaders()
                ->addHeaderLine('Content-Transfer-Encoding', 'binary')
                ->addHeaderLine('Content-Type', $pic->getFiletype());
            return $response;
        } else {
            return;
        }
    }

    /*
     * Defaul Action
     */
    public function thumbnail200Action()
    {
        $id = (int) $this->params()->fromQuery('id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        
        if ($token == '') {
            $token = null;
        }
        
        $criteria = array(
            'id' => $id,
            'checksum' => $checksum,
            'token' => $token,
            'markedForDeletion' => 0,
            'isPicture' => 1
        
        );
        
        $pic = new \Application\Entity\NmtApplicationAttachment();
        $pic = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findOneBy($criteria);
        
        if ($pic !== null) {
            $pic_folder = getcwd() . self::ATTACHMENT_FOLDER . DIRECTORY_SEPARATOR . $pic->getFolderRelative() . "thumbnail_200_" . $pic->getFileName();
            
            /**
             * Important! for UBUNTU
             */
            $pic_folder = str_replace('\\', '/', $pic_folder);
            
            $imageContent = file_get_contents($pic_folder);
            
            $response = $this->getResponse();
            
            $response->setContent($imageContent);
            $response->getHeaders()
                ->addHeaderLine('Content-Transfer-Encoding', 'binary')
                ->addHeaderLine('Content-Type', $pic->getFiletype());
            return $response;
        } else {
            return;
        }
    }

    /*
     * Defaul Action
     */
    public function thumbnail450Action()
    {
        $id = (int) $this->params()->fromQuery('id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        
        if ($token == '') {
            $token = null;
        }
        
        $criteria = array(
            'id' => $id,
            'checksum' => $checksum,
            'token' => $token,
            'markedForDeletion' => 0,
            'isPicture' => 1
        
        );
        
        $pic = new \Application\Entity\NmtApplicationAttachment();
        $pic = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findOneBy($criteria);
        if ($pic !== null) {
            
            $pic_folder = getcwd() . self::ATTACHMENT_FOLDER . DIRECTORY_SEPARATOR . $pic->getFolderRelative() . "thumbnail_450_" . $pic->getFileName();
            /**
             * Important! for UBUNTU
             */
            $pic_folder = str_replace('\\', '/', $pic_folder);
            
            $imageContent = file_get_contents($pic_folder);
            
            $response = $this->getResponse();
            
            $response->setContent($imageContent);
            $response->getHeaders()
                ->addHeaderLine('Content-Transfer-Encoding', 'binary')
                ->addHeaderLine('Content-Type', $pic->getFiletype());
            return $response;
        } else {
            return;
        }
    }

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
     */
    public function uploadAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            $target_id = (int) $request->getPost('target_id');
            $token = $request->getPost('token');
            
            $criteria = array(
                'id' => $target_id,
                'token' => $token
            );
            
            /**
             *
             * @todo : Change Target
             * @var \Application\Entity\NmtProcureQo $target ;
             *     
             */
            $target = $this->doctrineEM->getRepository('Application\Entity\NmtProcureQo')->findOneBy($criteria);
            
            if (! $target instanceof \Application\Entity\NmtProcureQo) {
                
                $errors[] = 'Target object can\'t be empty. Or token key is not valid!';
                $this->flashMessenger()->addMessage('Something wrong!');
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
                $validFrom = $request->getPost('validFrom');
                
                $isActive = $request->getPost('isActive');
                $markedForDeletion = $request->getPost('markedForDeletion');
                $filePassword = $request->getPost('filePassword');
                $visibility = $request->getPost('visibility');
                
                $entity = new NmtApplicationAttachment();
                
                /**
                 *
                 * @todo : updated.
                 */
                $entity->setQo($target);
                $entity->setTargetClass(get_class($target));
                $entity->setTargetId($target->getId());
                
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
                
                if ($filePassword === null or $filePassword == "") {
                    $filePassword = self::PDF_PASSWORD;
                }
                
                $entity->setIsActive($isActive);
                $entity->setMarkedForDeletion($markedForDeletion);
                $entity->setVisibility($visibility);
                // validator.
                $validator = new Date();
                $date_to_validate = 2;
                $date_validated = 0;
                
                // Empty is OK
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
                
                // Empty is OK
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
                
                if ($target->getVendor() instanceof \Application\Entity\NmtBpVendor) {
                    $entity->setVendor($target->getVendor());
                }
                
                if (isset($_FILES['attachments'])) {
                    $file_name = $_FILES['attachments']['name'];
                    $file_size = $_FILES['attachments']['size'];
                    $file_tmp = $_FILES['attachments']['tmp_name'];
                    $file_type = $_FILES['attachments']['type'];
                    $file_ext = strtolower(end(explode('.', $_FILES['attachments']['name'])));
                    
                    // attachement required?
                    if ($file_tmp == "" or $file_tmp === null) {
                        
                        $errors[] = 'Attachment can\'t be empty!';
                        $this->flashMessenger()->addMessage('Something wrong!');
                        return new ViewModel(array(
                            'redirectUrl' => $redirectUrl,
                            'errors' => $errors,
                            'target' => $target,
                            'entity' => $entity
                        ));
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
                            $errors[] = 'File size must be  10 MB';
                        }
                        
                        $checksum = md5_file($file_tmp);
                        
                        /**
                         *
                         * @todo : Update Targert
                         */
                        $criteria = array(
                            "checksum" => $checksum,
                            'targetId' => $target_id,
                            'targetClass' => get_class($target)
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
                        
                        // NO ERRORS
                        // Saving into DB....................
                        // ==================================
                        
                        $name_part1 = Rand::getString(6, self::CHAR_LIST, true) . "_" . Rand::getString(10, self::CHAR_LIST, true);
                        
                        $name = md5($target_id . $checksum . uniqid(microtime())) . '_' . $name_part1 . '.' . $ext;
                        
                        $folder_relative = $name[0] . $name[1] . DIRECTORY_SEPARATOR . $name[2] . $name[3] . DIRECTORY_SEPARATOR . $name[4] . $name[5];
                        $folder = ROOT . self::ATTACHMENT_FOLDER . DIRECTORY_SEPARATOR . $folder_relative;
                        
                        if (! is_dir($folder)) {
                            mkdir($folder, 0777, true); // important
                        }
                        
                        // echo ("$folder/$name");
                        move_uploaded_file($file_tmp, "$folder/$name");
                        
                        if ($isPicture == 1) {
                            // trigger uploadPicture. AbtractController is EventManagerAware.
                            $this->getEventManager()->trigger('uploadPicture', __CLASS__, array(
                                'picture_name' => $name,
                                'pictures_dir' => $folder
                            ));
                        }
                        
                        if ($ext == "pdf") {
                            $pdf_box = ROOT . self::PDFBOX_FOLDER;
                            
                            // java -jar pdfbox-app-2.0.5.jar Encrypt [OPTIONS] <password> <inputfile>
                            exec('java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar Encrypt -O mla2017 -U ' . $filePassword . ' ' . "$folder/$name");
                            
                            // extract text:
                            exec('java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar ExtractText -password ' . $filePassword . ' ' . "$folder/$name" . ' ' . "$folder/$name" . '.txt');
                        }
                        // update database
                        $entity->setFilePassword($filePassword);
                        $entity->setIsPicture($isPicture);
                        $entity->setFilename($name);
                        $entity->setFiletype($file_type);
                        $entity->setFilenameOriginal($file_name);
                        $entity->setSize($file_size);
                        $entity->setFolder($folder);
                        // new
                        $entity->setAttachmentFolder(self::ATTACHMENT_FOLDER . DIRECTORY_SEPARATOR . $folder_relative . DIRECTORY_SEPARATOR);
                        
                        $entity->setFolderRelative($folder_relative . DIRECTORY_SEPARATOR);
                        $entity->setChecksum($checksum);
                        $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                        
                        $entity->setCreatedBy($u);
                        $entity->setCreatedOn(new \DateTime());
                        $this->doctrineEM->persist($entity);
                        $this->doctrineEM->flush();
                        
                        $this->flashMessenger()->addMessage("'" . $file_name . "' has been uploaded successfully!");
                        return $this->redirect()->toUrl($redirectUrl);
                    }
                }
            }
        }
        
        // NO POST
        // Initiating....................
        // ================================
        
        $redirectUrl = null;
        if ($this->getRequest()->getHeader('Referer') != null) {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }
        
        $id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );
        
        /**
         *
         * @todo : Change Target
         * @var \Application\Entity\NmtProcureQo $target ;
         */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtProcureQo')->findOneBy($criteria);
        
        if ($target instanceof \Application\Entity\NmtProcureQo) {
            
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'target' => $target,
                'entity' => null
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @deprecated
     *
     * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
     */
    public function uploadPictureAction()
    {
        $request = $this->getRequest();
        $redirectUrl = null;
        
        if ($request->getHeader('Referer') == null) {
            // return $this->redirect ()->toRoute ( 'access_denied' );
        } else {
            $redirectUrl = $request->getHeader('Referer')->getUri();
        }
        
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));
        
        if ($request->isPost()) {
            
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            $target_id = $request->getPost('target_id');
            $token = $request->getPost('token');
            
            $criteria = array(
                'id' => $target_id,
                'token' => $token
            );
            
            /**
             *
             * @todo : Change Target
             * @var \Application\Entity\NmtProcureQo $target ;
             *     
             */
            $target = $this->doctrineEM->getRepository('Application\Entity\NmtProcureQo')->findOneBy($criteria);
            
            if ($target instanceof \Application\Entity\NmtProcureQo) {
                
                $errors[] = 'Target object can\'t be empty. Token key might be not valid. Please try again!!';
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
                $validFrom = $request->getPost('validFrom');
                
                $isActive = $request->getPost('isActive');
                $markedForDeletion = $request->getPost('markedForDeletion');
                $filePassword = $request->getPost('filePassword');
                $visibility = $request->getPost('visibility');
                
                $entity = new NmtApplicationAttachment();
                
                // Target: EMPLOYEE
                $entity->setEmployee($target);
                
                $remarks = $request->getPost('remarks');
                
                /*
                 * if ($documentSubject == null) {
                 * $errors [] = 'Please give document subject!';
                 * } else {
                 * $entity->setDocumentSubject ( $documentSubject );
                 * }
                 */
                
                if ($documentSubject == null) {
                    $documentSubject = 'Picture';
                }
                $entity->setDocumentSubject($documentSubject);
                
                if ($isActive != 1) {
                    $isActive = 0;
                }
                
                if ($markedForDeletion != 1) {
                    $markedForDeletion = 0;
                }
                
                if ($visibility != 1) {
                    $visibility = 0;
                }
                
                if ($filePassword === null or $filePassword == "") {
                    $filePassword = self::PDF_PASSWORD;
                }
                
                $entity->setIsActive(1);
                $entity->setMarkedForDeletion(0);
                $entity->setVisibility(1);
                // validator.
                $validator = new Date();
                $date_to_validate = 2;
                $date_validated = 0;
                
                // Empty is OK
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
                
                // Empty is OK
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
                
                // need to set context id
                $vendor = null;
                if ($vendor_id > 0) {
                    $vendor = $this->doctrineEM->find('Application\Entity\NmtBpVendor', $vendor_id);
                    $entity->setVendor($vendor);
                }
                
                if (isset($_FILES['attachments'])) {
                    $file_name = $_FILES['attachments']['name'];
                    $file_size = $_FILES['attachments']['size'];
                    $file_tmp = $_FILES['attachments']['tmp_name'];
                    $file_type = $_FILES['attachments']['type'];
                    $file_ext = strtolower(end(explode('.', $_FILES['attachments']['name'])));
                    
                    // attachement required?
                    if ($file_tmp == "" or $file_tmp === null) {
                        
                        $errors[] = 'Attachment can\'t be empty!';
                        return new ViewModel(array(
                            'redirectUrl' => $redirectUrl,
                            'errors' => $errors,
                            'target' => $target,
                            'entity' => $entity
                        ));
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
                            "gif"
                        );
                        
                        if (in_array($ext, $expensions) === false) {
                            $errors[] = 'Extension file"' . $ext . '" not supported, please choose a "jpeg","jpg","png","gif"!';
                        }
                        
                        if ($file_size > 2097152) {
                            $errors[] = 'File size must be  2 MB';
                        }
                        
                        $checksum = md5_file($file_tmp);
                        
                        // change target
                        $criteria = array(
                            "checksum" => $checksum,
                            "employee" => $target_id
                        );
                        $ck = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findby($criteria);
                        
                        if (count($ck) > 0) {
                            $errors[] = 'Document: "' . $file_name . '"  exits already';
                        }
                        
                        if (count($errors) > 0) {
                            
                            return new ViewModel(array(
                                'redirectUrl' => $redirectUrl,
                                'errors' => $errors,
                                'target' => $target,
                                'entity' => $entity
                            ));
                        }
                        ;
                        
                        $name_part1 = Rand::getString(6, self::CHAR_LIST, true) . "_" . Rand::getString(10, self::CHAR_LIST, true);
                        $name = md5($target_id . $checksum . uniqid(microtime())) . '_' . $name_part1 . '.' . $ext;
                        
                        $folder_relative = $name[0] . $name[1] . DIRECTORY_SEPARATOR . $name[2] . $name[3] . DIRECTORY_SEPARATOR . $name[4] . $name[5];
                        $folder = ROOT . self::ATTACHMENT_FOLDER . DIRECTORY_SEPARATOR . $folder_relative;
                        
                        if (! is_dir($folder)) {
                            mkdir($folder, 0777, true); // important
                        }
                        
                        // echo ("$folder/$name");
                        move_uploaded_file($file_tmp, "$folder/$name");
                        
                        // trigger uploadPicture. AbtractController is EventManagerAware.
                        $this->getEventManager()->trigger('uploadPicture', __CLASS__, array(
                            'picture_name' => $name,
                            'pictures_dir' => $folder
                        ));
                        
                        /*
                         * if ($ext == "pdf") {
                         * $pdf_box = ROOT . self::PDFBOX_FOLDER;
                         *
                         * // java -jar pdfbox-app-2.0.5.jar Encrypt [OPTIONS] <password> <inputfile>
                         * exec ( 'java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar Encrypt -O mla2017 -U ' . $filePassword . ' ' . "$folder/$name" );
                         *
                         * // extract text:
                         * exec ( 'java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar ExtractText -password ' . $filePassword . ' ' . "$folder/$name" . ' ' . "$folder/$name" . '.txt' );
                         * }
                         */
                        
                        // update database
                        $entity->setFilePassword($filePassword);
                        $entity->setIsPicture($isPicture);
                        $entity->setFilename($name);
                        $entity->setFiletype($file_type);
                        $entity->setFilenameOriginal($file_name);
                        $entity->setSize($file_size);
                        $entity->setFolder($folder);
                        // new
                        $entity->setAttachmentFolder(self::ATTACHMENT_FOLDER . DIRECTORY_SEPARATOR . $folder_relative . DIRECTORY_SEPARATOR);
                        
                        $entity->setFolderRelative($folder_relative . DIRECTORY_SEPARATOR);
                        $entity->setChecksum($checksum);
                        $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                        
                        $entity->setCreatedBy($u);
                        $entity->setCreatedOn(new \DateTime());
                        $this->doctrineEM->persist($entity);
                        $this->doctrineEM->flush();
                        
                        $this->flashMessenger()->addMessage("'" . $file_name . "' has been uploaded!");
                        return $this->redirect()->toUrl($redirectUrl);
                    }
                }
            }
        }
        
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        
        $id = (int) $this->params()->fromQuery('target_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'checksum' => $checksum,
            'token' => $token
        );
        
        // Target: Employee
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findOneBy($criteria);
        
        if ($target !== null) {
            
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'target' => $target,
                'entity' => null
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function uploadPicturesAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            
            $errors = array();
            $pictures = $_POST['pictures'];
            $target_id = $_POST['target_id'];
            $checksum = $_POST['checksum'];
            $token = $_POST['token'];
            $documentSubject = $_POST['subject'];
            $entity_id = $_POST['entity_id'];
            $entity_token = $_POST['entity_token'];
            
            $criteria = array(
                'id' => $target_id,
                'token' => $token
            );
            
            /**@var \Application\Entity\NmtProcureQo $target ;*/
            $target = $this->doctrineEM->getRepository('Application\Entity\NmtProcureQo')->findOneBy($criteria);
            
            if (! $target instanceof \Application\Entity\NmtProcureQo) {
                
                $errors[] = 'Target object can\'t be empty. Token key might be not valid. Please try again!!';
                $this->flashMessenger()->addMessage('Something wrong!');
                
                return new ViewModel(array(
                    'redirectUrl' => null,
                    'errors' => $errors,
                    'target' => null,
                    'entity' => null
                ));
                
                // might need redirect
            } else {
                
                $result = array();
                $success = 0;
                $failed = 0;
                $n = 0;
                foreach ($pictures as $p) {
                    $n ++;
                    $filetype = $p[0];
                    $original_filename = $p[2];
                    
                    if (preg_match('/(jpg|jpeg)$/', $filetype)) {
                        $ext = 'jpg';
                    } else if (preg_match('/(gif)$/', $filetype)) {
                        $ext = 'gif';
                    } else if (preg_match('/(png)$/', $filetype)) {
                        $ext = 'png';
                    }
                    
                    // fix unix folder.
                    $tmp_name = ROOT . "/temp/" . md5($target_id . uniqid(microtime())) . '.' . $ext;
                    
                    // remove "data:image/png;base64,"
                    $uri = substr($p[1], strpos($p[1], ",") + 1);
                    
                    // save to file
                    file_put_contents($tmp_name, base64_decode($uri));
                    $checksum = md5_file($tmp_name);
                    
                    /**
                     *
                     * @todo: Update target
                     */
                    $criteria = array(
                        "checksum" => $checksum,
                        "qo" => $target_id
                    );
                    $ck = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findby($criteria);
                    
                    if (count($ck) == 0) {
                        $name_part1 = Rand::getString(6, self::CHAR_LIST, true) . "_" . Rand::getString(10, self::CHAR_LIST, true);
                        $name = md5($target_id . $checksum . uniqid(microtime())) . '_' . $name_part1 . '.' . $ext;
                        
                        $folder_relative = $name[0] . $name[1] . DIRECTORY_SEPARATOR . $name[2] . $name[3] . DIRECTORY_SEPARATOR . $name[4] . $name[5];
                        $folder = ROOT . self::ATTACHMENT_FOLDER . DIRECTORY_SEPARATOR . $folder_relative;
                        
                        /**
                         * Important! for UBUNTU
                         */
                        $folder = str_replace('\\', '/', $folder);
                        
                        if (! is_dir($folder)) {
                            mkdir($folder, 0777, true); // important
                        }
                        
                        // echo ("$folder/$name");
                        // move_uploaded_file ( $tmp_name, "$folder/$name" );
                        rename($tmp_name, "$folder/$name");
                        
                        $entity = new NmtApplicationAttachment();
                        
                        /**
                         *
                         * @todo: Update target
                         */
                        $entity->setQo($target);
                        $entity->setTargetClass(get_class($target));
                        $entity->setTargetId($target->getId());
                        
                        // $entity->setFilePassword ( $filePassword );
                        if ($documentSubject == null) {
                            $documentSubject = "Picture for " . $target_id;
                        }
                        $entity->setDocumentSubject($documentSubject);
                        
                        $entity->setIsPicture(1);
                        $entity->setIsActive(1);
                        $entity->setMarkedForDeletion(0);
                        $entity->setFilename($name);
                        $entity->setFiletype($filetype);
                        $entity->setFilenameOriginal($original_filename);
                        // $entity->setSize ( $file_size );
                        $entity->setFolder($folder);
                        // new
                        $entity->setAttachmentFolder(self::ATTACHMENT_FOLDER . DIRECTORY_SEPARATOR . $folder_relative . DIRECTORY_SEPARATOR);
                        
                        $entity->setFolderRelative($folder_relative . DIRECTORY_SEPARATOR);
                        $entity->setChecksum($checksum);
                        $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                        
                        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                            "email" => $this->identity()
                        ));
                        
                        $createdOn = new \DateTime();
                        
                        $entity->setCreatedBy($u);
                        $entity->setCreatedOn($createdOn);
                        
                        // get Old Entity, if any
                        $criteria = array(
                            'id' => $entity_id,
                            'token' => $entity_token
                        );
                        $old_entity = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findOneBy($criteria);
                        
                        if ($old_entity instanceof \Application\Entity\NmtApplicationAttachment) {
                            $old_entity->setIsActive(0);
                            $old_entity->setMarkedForDeletion(1);
                            $old_entity->setLastChangeBy($u);
                            $old_entity->setLastChangeOn(new \DateTime());
                            $entity->setChangeFor($old_entity->getId());
                            
                            $m = sprintf('[INFO] %s updated with new file.', $old_entity->getDocumentSubject());
                            // $this->flashMessenger()->addMessage("'" . $old_entity->getDocumentSubject() . "' has been update with new file!");
                        } else {
                            // $this->flashMessenger()->addMessage("'" . $original_filename . "' has been uploaded sucessfully");
                            $m = sprintf('[OK] %s uploaded.', $original_filename);
                        }
                        
                        $this->flashMessenger()->addMessage($m);
                        
                        $this->doctrineEM->persist($entity);
                        $this->doctrineEM->flush();
                        
                        $result[] = $original_filename . ' uploaded sucessfully';
                        $success ++;
                        
                        // Trigger uploadPicture. AbtractController is EventManagerAware.
                        $this->getEventManager()->trigger('uploadPicture', __METHOD__, array(
                            'picture_name' => $name,
                            'pictures_dir' => $folder
                        ));
                        
                        $m = sprintf('[OK] Image #%s for Quotation #%s - %s uploaded.', $entity->getId(), $target->getId(), $target->getSysNumber());
                        
                        // Trigger Activity Log . AbtractController is EventManagerAware.
                        $this->getEventManager()->trigger('procure.activity.log', __METHOD__, array(
                            'priority' => \Zend\Log\Logger::INFO,
                            'message' => $m,
                            'createdBy' => $u,
                            'createdOn' => $createdOn
                        ));
                    } else {
                        $this->flashMessenger()->addMessage(sprintf('[INFO] %s exits!', $original_filename));
                        $result[] = $original_filename . ' exits already. Please select other file!';
                        $failed ++;
                    }
                }
                
                // $data['filetype'] = $filetype;
                $data = array();
                $data['message'] = $result;
                $data['total_uploaded'] = $n;
                $data['success'] = $success;
                $data['failed'] = $failed;
                $response = $this->getResponse();
                $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
                $response->setContent(json_encode($data));
                return $response;
            }
        }
        
        // NO POST
        // Initiating.............
        // ========================
        
        $redirectUrl = null;
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        } else {
            $redirectUrl = $request->getHeader('Referer')->getUri();
        }
        
        $id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );
        
        /**@var \Application\Entity\NmtProcureQo $target ;*/
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtProcureQo')->findOneBy($criteria);
        
        if ($target instanceof \Application\Entity\NmtProcureQo) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'target' => $target,
                'entity' => null
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function downloadAction()
    {
        $request = $this->getRequest();
        
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        
        if ($token == '') {
            $token = null;
        }
        
        $criteria = array(
            'id' => $entity_id,
            'checksum' => $checksum,
            'token' => $token
            // 'markedForDeletion' => 0,
        );
        
        $attachment = new NmtApplicationAttachment();
        $tmp_attachment = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findOneBy($criteria);
        $attachment = $tmp_attachment;
        
        if ($attachment !== null) {
            $f = ROOT . $attachment->getAttachmentFolder() . DIRECTORY_SEPARATOR . $attachment->getFilename();
            $output = file_get_contents($f);
            
            $response = $this->getResponse();
            $headers = new Headers();
            
            $headers->addHeaderLine('Content-Type: ' . $attachment->getFiletype());
            $headers->addHeaderLine('Content-Disposition: attachment; filename="' . $attachment->getFilenameOriginal() . '"');
            $headers->addHeaderLine('Content-Description: File Transfer');
            $headers->addHeaderLine('Content-Transfer-Encoding: binary');
            $headers->addHeaderLine('Content-Encoding: UTF-8');
            
            $response->setHeaders($headers);
            
            $response->setContent($output);
            return $response;
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
         * @todo: update target
         */
        $query = 'SELECT e FROM Application\Entity\NmtApplicationAttachment e WHERE e.pr > :n';
        
        $list = $this->doctrineEM->createQuery($query)
            ->setParameter('n', 0)
            ->getResult();
        
        if (count($list) > 0) {
            foreach ($list as $entity) {
                /**
                 *
                 * @todo Update Target
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
