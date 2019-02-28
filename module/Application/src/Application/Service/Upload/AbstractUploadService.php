<?php
namespace Application\Service\Upload;

use Doctrine\ORM\EntityManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Math\Rand;
use Zend\Validator\Date;

/**
 * Upload Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractUploadService implements EventManagerAwareInterface
{

    protected $doctrineEM;

    protected $controllerPlugin;

    protected $eventManager;

    protected $uploadPath;

    protected $targetId;

    protected $fileTmp;

    protected $targetClass;

    protected $targetObject;

    protected $targetToken;

    protected $attachmentEntity;

    protected $targetEntity;

    public function __construct()
    {}

    /**
     *
     * @param int $target_id
     * @param string $target_token
     */
    abstract function setTarget($target_id, $target_token);

    /**
     * set Upload path
     */
    abstract function setUploadPath();

    /**
     * 
     * @param string $m
     * @param \Application\Entity\MlaUsers $u
     * @param \DateTime $createdOn
     */
    abstract function doLogging($priority, $m,$u,$createdOn);

    /**
     *
     * @param \Application\Entity\NmtApplicationAttachment $entity
     * @param array $header_data
     * @param int $target_id
     * @param string $target_token
     * @return NULL[]|string[]
     */
    public function validateHeader(\Application\Entity\NmtApplicationAttachment $entity, $header_data)
    {
        $errors = array();

        if (! $entity instanceof \Application\Entity\NmtApplicationAttachment) {
            $errors[] = $this->controllerPlugin->translate('Attachment Object is not found!');
        }

        if ($header_data == null) {
            $errors[] = $this->controllerPlugin->translate('No input given');
        }

        $documentSubject = $header_data['documentSubject'];
        $validFrom = $header_data['validFrom'];
        $validTo = $header_data['validTo'];

        $isActive = $header_data['isActive'];
        $markedForDeletion = $header_data['markedForDeletion'];
        $filePassword = $header_data['filePassword'];
        $visibility = $header_data['visibility'];
        $remarks = $header_data['remarks'];

        if ($documentSubject == null) {
            $errors[] = $this->controllerPlugin->translate('Please give document subject!');
        } else {
            $entity->setDocumentSubject($documentSubject);
        }

        // validator.
        $validator = new Date();
        $date_to_validate = 2;
        $date_validated = 0;

        // Empty is OK
        if ($validFrom !== null) {
            if ($validFrom !== "") {
                if (! $validator->isValid($validFrom)) {
                    $errors[] = $this->controllerPlugin->translate('Start date is not correct or empty!');
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
                    $errors[] = $this->controllerPlugin->translate('End date is not correct or empty!');
                } else {
                    $date_validated ++;
                    $entity->setValidTo(new \DateTime($validTo));
                }
            }
        }

        // all date corrected
        if ($date_validated == $date_to_validate) {

            if ($validFrom > $validTo) {
                $errors[] = $this->controllerPlugin->translate('End date must be in future!');
            }
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

        $entity->setIsActive($isActive);
        $entity->setMarkedForDeletion($markedForDeletion);
        $entity->setVisibility($visibility);
        $entity->setFilePassword($filePassword);

        $entity->setRemarks($remarks);

        return $errors;
    }

    /**
     * Uploading one file
     *
     * @param \Application\Entity\NmtApplicationAttachment $entity
     * @param array $uploaded_file
     * @return string[]
     */
    public function validateAttachment(\Application\Entity\NmtApplicationAttachment $entity, $attachments, $target_class, $target_id, $isNew)
    {
        $errors = array();

        if ($attachments == null) {
            $errors[] = $this->controllerPlugin->translate('Attachment is not found!');
        }

        $file_name = $attachments['name'];
        $file_size = $attachments['size'];
        $file_tmp = $attachments['tmp_name'];
        $file_type = $attachments['type'];

        // to fix PHP: Only variables should be passed by reference
        $file_ext_tmp = explode('.', $file_name);
        $file_ext = strtolower(end($file_ext_tmp));

        // attachement required?
        if ($file_tmp == "" or $file_tmp === null) {
            $errors[] = $this->controllerPlugin->translate('Attachment can\'t be empty!');
        } else {

            $this->fileTmp = $file_tmp;

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

            $entity->setFileExtension($ext);
            $entity->setIsPicture($isPicture);

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
                $errors[] = $ext . $this->controllerPlugin->translate(' not supported, please choose a "jpeg","jpg","png","pdf","xlsx","xlx", "docx"!');
            }

            // Set Global
            if ($file_size > 2097152) {
                $errors[] = $this->controllerPlugin->translate('File size must be 2 MB');
            }

            $checksum = md5_file($file_tmp);

            /**
             *
             * @todo : Update Target
             */
            $criteria = array(
                "checksum" => $checksum,
                'targetId' => $target_id,
                'targetClass' => $target_class
            );

            $ck = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findby($criteria);

            if (count($ck) > 0) {
                $errors[] = $file_name . $this->controllerPlugin->translate(' exits already');
            } else {
                $entity->setChecksum($checksum);
                $entity->setFiletype($file_type);
                $entity->setFilenameOriginal($file_name);
                $entity->setSize($file_size);
            }

            return $errors;
        }
    }

    /**
     * Upload one file with POST
     *
     * @param \Application\Entity\NmtApplicationAttachment $entity
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isNew
     */
    public function doUploading($entity = null, $target_id, $target_token, $header_data, $attachments, $u, $isNew = TRUE)
    {
        $errors = array();

        if ($u == null) {
            $errors[] = $this->controllerPlugin->translate("Invalid Argument! User can't be indentided for this transaction.");
            ;
        }

        if ($isNew == FALSE and $entity == null) {
            $errors[] = $this->controllerPlugin->translate("Invalid Argument! Attachment Entity not found for editing");
        }

        if ($isNew == TRUE and $entity == null) {
            $entity = new \Application\Entity\NmtApplicationAttachment();
        }

        $this->setAttachmentEntity($entity);

        if ($isNew == TRUE) {
            // template
            $this->setTarget($target_id, $target_token);
        }

        // template
        $this->setUploadPath();

        // validate header.
        $ck1 = $this->validateHeader($entity, $header_data);

        if (count($ck1) > 0) {
            $errors = array_merge($errors, $ck1);
        }

        $ck2 = $this->validateAttachment($entity, $attachments, $this->getAttachmentEntity()
            ->getTargetClass(), $this->getAttachmentEntity()
            ->getTargetId(), $isNew);

        if (count($ck1) > 0) {
            $errors = array_merge($errors, $ck2);
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // validated.
        $name_part1 = Rand::getString(6, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true);
        $name = md5($target_id . $entity->getChecksum() . uniqid(microtime())) . '_' . $name_part1 . '.' . $entity->getFileExtension();

        $folder_relative = $name[0] . $name[1] . DIRECTORY_SEPARATOR . $name[2] . $name[3] . DIRECTORY_SEPARATOR . $name[4] . $name[5];
        $folder = ROOT . $this->uploadPath . DIRECTORY_SEPARATOR . $folder_relative;

        /**
         * Important! for UBUNTU
         */
        $folder = str_replace('\\', '/', $folder);

        if (! is_dir($folder)) {
            mkdir($folder, 0777, true); // important
        }

        // echo ("$folder/$name");
        move_uploaded_file($this->fileTmp, "$folder/$name");

        // update database

        $entity->setFilename($name);
        $entity->setFolder($folder);

        // new
        $entity->setAttachmentFolder($this->uploadPath . DIRECTORY_SEPARATOR . $folder_relative . DIRECTORY_SEPARATOR);

        $entity->setFolderRelative($folder_relative . DIRECTORY_SEPARATOR);
        $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));

        $createdOn = new \DateTime();

        if ($isNew == TRUE) {
            $entity->setCreatedBy($u);
            $entity->setCreatedOn($createdOn);
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
        }

        if ($entity->getIsPicture() == 1) {
            // trigger uploadPicture. AbtractController is EventManagerAware.
            $this->getEventManager()->trigger('uploadPicture', __CLASS__, array(
                'picture_name' => $name,
                'pictures_dir' => $folder
            ));
        }
    }

    public function doUploadPictures($postArray, $u)
    {
        $pictures = $postArray['pictures'];
        $target_id = $postArray['target_id'];
        $checksum = $postArray['checksum'];
        $target_token = $postArray['token'];
        $documentSubject = $postArray['subject'];
        $entity_id = $postArray['entity_id'];
        $entity_token = $postArray['entity_token'];

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

            $entity = new \Application\Entity\NmtApplicationAttachment();
            $this->setAttachmentEntity($entity);

            // Template Function
            $this->setTarget($target_id, $target_token);

            // Template Function
            $this->setUploadPath();

            $criteria = array(
                "checksum" => $checksum,
                'targetId' => $target_id,
                'targetClass' => $entity->getTargetClass()
            );
            $ck = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findby($criteria);
            
            $createdOn = new \DateTime();
            
            if (count($ck) == 0) {
                $name_part1 = Rand::getString(6, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true);
                $name = md5($target_id . $checksum . uniqid(microtime())) . '_' . $name_part1 . '.' . $ext;

                $folder_relative = $name[0] . $name[1] . DIRECTORY_SEPARATOR . $name[2] . $name[3] . DIRECTORY_SEPARATOR . $name[4] . $name[5];
                $folder = ROOT . $this->uploadPath . DIRECTORY_SEPARATOR . $folder_relative;

                /**
                 * Important! for UBUNTU
                 */
                $folder = str_replace('\\', '/', $folder);

                if (! is_dir($folder)) {
                    mkdir($folder, 0777, true); // important
                }

                rename($tmp_name, "$folder/$name");

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
                $entity->setAttachmentFolder($this->uploadPath . DIRECTORY_SEPARATOR . $folder_relative . DIRECTORY_SEPARATOR);

                $entity->setFolderRelative($folder_relative . DIRECTORY_SEPARATOR);
                $entity->setChecksum($checksum);
                $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));

                $entity->setCreatedBy($u);
                $entity->setCreatedOn($createdOn);

                // What is it?
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

                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();

                $result[] = $original_filename . ' uploaded sucessfully';
                $success ++;

                // Trigger uploadPicture. AbtractController is EventManagerAware.
                $this->getEventManager()->trigger('uploadPicture', __METHOD__, array(
                    'picture_name' => $name,
                    'pictures_dir' => $folder
                ));

                $m = sprintf('[OK] uploaded.', $entity->getId(), $entity->getTargetId(), "");
                $this->doLogging(\Zend\Log\Logger::INFO, $m, $u, $createdOn);
                
            } else {
                
                $m = sprintf('[FAILED].', $entity->getId(), $entity->getTargetId(), "");
                $this->doLogging(\Zend\Log\Logger::WARN, $m, $u, $createdOn);
                
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
        return $data;
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @return \Procure\Service\GrService
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Zend\EventManager\EventManagerAwareInterface::setEventManager()
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers(array(
            __CLASS__
        ));
        $this->eventManager = $eventManager;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Zend\EventManager\EventsCapableInterface::getEventManager()
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     *
     * @return \Application\Controller\Plugin\NmtPlugin
     */
    public function getControllerPlugin()
    {
        return $this->controllerPlugin;
    }

    /**
     *
     * @param \Application\Controller\Plugin\NmtPlugin $controllerPlugin
     */
    public function setControllerPlugin(\Application\Controller\Plugin\NmtPlugin $controllerPlugin)
    {
        $this->controllerPlugin = $controllerPlugin;
    }

    /**
     *
     * @return \Application\Entity\NmtApplicationAttachment
     */
    public function getAttachmentEntity()
    {
        return $this->attachmentEntity;
    }

    /**
     *
     * @param \Application\Entity\NmtApplicationAttachment $attachmentEntity
     */
    public function setAttachmentEntity(\Application\Entity\NmtApplicationAttachment $attachmentEntity)
    {
        $this->attachmentEntity = $attachmentEntity;
    }

    /**
     *
     * @return mixed
     */
    public function getTargetEntity()
    {
        return $this->targetEntity;
    }
    
    /**
     * @param mixed $targetEntity
     */
    public function setTargetEntity($targetEntity)
    {
        $this->targetEntity = $targetEntity;
    }
}
