<?php
namespace Application\Service;

use Zend\Math\Rand;
use Zend\Validator\Date;

/**
 * Upload Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class UploadService extends AbstractService
{

    /**
     *
     * @param \Application\Entity\NmtApplicationAttachment $entity
     * @param array $data
     * @/param boolean $isPosting
     */
    public function validateHeader(\Application\Entity\NmtApplicationAttachment $entity,$data,$target_class, $target_id)
    {
        $errors = array();

        if (! $entity instanceof \Application\Entity\NmtApplicationAttachment) {
            $errors[] = $this->controllerPlugin->translate('Attachment Object is not found!');
        }

        if ($data == null) {
            $errors[] = $this->controllerPlugin->translate('No input given');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        return $errors;
    }

    /**
     * 
     * @param \Application\Entity\NmtApplicationAttachment $entity
     * @param array $uploaded_file
     * @return string[]
     */
    public function validateAttachment(\Application\Entity\NmtApplicationAttachment $entity, $uploaded_file,$target_class, $target_id)
    {
        $errors = array();

        if ($uploaded_file == null) {
            $errors[] = $this->controllerPlugin->translate('Attachment is not found!');
        }

        $file_name = $uploaded_file['name'];
        $file_size = $uploaded_file['size'];
        $file_tmp = $uploaded_file['tmp_name'];
        $file_type = $uploaded_file['type'];
        $file_ext = strtolower(end(explode('.', $uploaded_file['name'])));

     
        // attachement required?
        if ($file_tmp == "" or $file_tmp === null) {
            $errors[] = 'Attachment can\'t be empty!';
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
                $errors[] = 'Extension file"' . $ext . '" not supported, please choose a "jpeg","jpg","png","pdf","xlsx","xlx", "docx"!';
            }

            if ($file_size > 2097152) {
                $errors[] = 'File size must be  2 MB';
            }

            $checksum = md5_file($file_tmp);
            
            /**
             *
             * @todo : Update Targert
             */
            $criteria = array(
                "checksum" => $checksum,
                'targetId' => $target_id,
                'targetClass' => $target_class
            );
            
            $ck = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findby($criteria);
            
            if (count($ck) > 0) {
                $errors[] = 'Document: "' . $file_name . '"  exits already';
            }
        
            return $errors;
        }
    }

    /**
     *
     * @param \Application\Entity\NmtApplicationAttachment $entity
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isNew
     */
    public function doUploading($entity, $u, $isNew = FALSE)
    {
        if ($u == null) {
            $m = $this->controllerPlugin->translate("Invalid Argument! User can't be indentided for this transaction.");
            throw new \Exception($m);
        }

        if (! $entity instanceof \Application\Entity\NmtApplicationAttachment) {
            $m = $this->controllerPlugin->translate("Invalid Argument! Attachment Object not found!");
            throw new \Exception($m);
        }

        // validated.

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
        
        $createdOn = new \DateTime();
        
        $entity->setCreatedBy($u);
        $entity->setCreatedOn($createdOn);
        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();
        
    }
}
