<?php
namespace Procure\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use MLA\Paginator;
use Application\Entity\NmtApplicationAttachment;
use Zend\Http\Headers;
use Zend\Validator\Date;
use Zend\Math\Rand;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrRowAttachmentController extends AbstractActionController
{

    /**
     *
     * @todo : TO UPDATE
     */
    const ATTACHMENT_FOLDER = "/data/procure/attachment/pr";

    const PDFBOX_FOLDER = "/vendor/pdfbox/";

    const PDF_PASSWORD = "mla2017";

    const CHAR_LIST = "_0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $doctrineEM;

    protected $prRowAttachmentService;

    /**
     *
     * @return \Procure\Service\Upload\PrRowUploadService
     */
    public function getPrRowAttachmentService()
    {
        return $this->prRowAttachmentService;
    }

    /**
     *
     * @param \Procure\Service\Upload\PrRowUploadService $prRowAttachmentService
     */
    public function setPrRowAttachmentService(\Procure\Service\Upload\PrRowUploadService $prRowAttachmentService)
    {
        $this->prRowAttachmentService = $prRowAttachmentService;
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

        if ($entity instanceof \Application\Entity\NmtApplicationAttachment) {

            /**
             *
             * @todo : Change Target
             * @var \Application\Entity\NmtApplicationAttachment $entity ;
             *     
             */
            $target = $entity->getPr();

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
            $data = $request->getPost();

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
            $target = $entity->getPr()->getP;

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
         * @var \Application\Entity\NmtProcurePrRow $target ;
         *     
         */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->findOneBy($criteria);

        if ($target instanceof \Application\Entity\NmtProcurePrRow) {
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
         * @var \Application\Entity\NmtProcurePrRow $target ;
         *     
         */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->findOneBy($criteria);

        if ($target instanceof \Application\Entity\NmtProcurePrRow) {

            /**
             *
             * @todo : Update Target
             */

            $criteria = array(
                'targetId' => $target_id,
                'targetClass' => get_class($target),
                'isActive' => 1,
                'markedForDeletion' => 0,
                'isPicture' => 1
            );

            /*
             * $criteria = array(
             * 'prRowId' => $target_id,
             * 'isActive' => 1,
             * 'markedForDeletion' => 0,
             * 'isPicture' => 1
             * );
             */

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

    /**
     *
     * @return void|\Zend\Stdlib\ResponseInterface
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

    /**
     *
     * @return void|\Zend\Stdlib\ResponseInterface
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
            $data = $request->getPost();
            $redirectUrl = $data['redirectUrl'];
            $target_id = $data['target_id'];
            $target_token = $data['token'];

            $criteria = array(
                'id' => $target_id,
                'token' => $target_token
            );

            /**
             *
             * @todo : Change Target
             * @var \Application\Entity\NmtProcurePrRow $target ;
             *     
             */
            $target = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->findOneBy($criteria);

            if (! $target instanceof \Application\Entity\NmtProcurePrRow) {

                $errors[] = 'Target object can\'t be empty. Or token key is not valid!';
                $this->flashMessenger()->addMessage('Something wrong!');
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'target' => null,
                    'entity' => null
                ));
            }

            $attachments = null;
            if (isset($_FILES['attachments'])) {
                $attachments = $_FILES['attachments'];
            }

            $errors = $this->getPrRowAttachmentService()->doUploading(null, $target_id, $target_token, $data, $attachments, $u, TRUE);

            if (count($errors) > 0) {
                $this->flashMessenger()->addMessage('Something wrong!');
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'target' => $target,
                    'entity' => $this->getPrRowAttachmentService()->getAttachmentEntity()
                ));
            }

            $m = sprintf('[OK] Attachment for PR line #%s added.', $target->getRowIdentifer());
            $this->flashMessenger()->addMessage($m);

            $createdOn = new \DateTime();
            // Trigger Activity Log . AbtractController is EventManagerAware.
            $this->getEventManager()->trigger('procure.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $createdOn
            ));
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiating....................
        // ================================

        $redirectUrl = null;
        if ($this->getRequest()->getHeader('Referer') != null) {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        } else {
            return $this->redirect()->toRoute('access_denied');
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
         * @var \Application\Entity\NmtProcurePrRow $target ;
         */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->findOneBy($criteria);

        if ($target instanceof \Application\Entity\NmtProcurePrRow) {

            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'target' => $target,
                'entity' => null
            ));
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
            
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            

            $errors = array();
            $postArray = $_POST;
            $target_id = $postArray['target_id'];
            $token = $postArray['token'];
      
            $criteria = array(
                'id' => $target_id,
                'token' => $token
            );

            /**@var \Application\Entity\NmtProcurePrRow $target ;*/
            $target = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->findOneBy($criteria);

            if (! $target instanceof \Application\Entity\NmtProcurePrRow) {

                $errors[] = 'Target object can\'t be empty. Token key might be not valid. Please try again!!';
                $this->flashMessenger()->addMessage('Something wrong!');

                return new ViewModel(array(
                    'redirectUrl' => null,
                    'errors' => $errors,
                    'target' => null,
                    'entity' => null
                ));
            }           
            $data = $this->prRowAttachmentService->doUploadPictures($postArray, $u);
            
            if(isset($data['message'])){
                foreach ($data['message'] as $m){
                    $this->flashMessenger()->addMessage($m);
                }
            }
   
            $this->flashMessenger()->addMessage('Success: ' . $data['success'] .'/'.$data['total_uploaded']);
            $this->flashMessenger()->addMessage('Failed: ' . $data['failed'].'/'.$data['total_uploaded']);
            
            
            $response = $this->getResponse();
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            return $response;
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

        /**@var \Application\Entity\NmtProcurePrRow $target ;*/
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->findOneBy($criteria);

        if ($target instanceof \Application\Entity\NmtProcurePrRow) {
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
            $f = ROOT . $attachment->getAttachmentFolder() . $attachment->getFilename();
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
