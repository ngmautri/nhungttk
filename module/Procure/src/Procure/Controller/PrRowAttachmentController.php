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
class PrRowAttachmentController extends AbstractActionController
{
    protected $doctrineEM;
    protected $attachmentService;

   
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

            $redirectUrl = $data['redirectUrl'];
            $entity_id = (int) $data['entity_id'];
            $entity_token =$data['entity_token'];
            $nTry = $data['n'];
            
            $criteria = array(
                'id' => $entity_id,
                'token' => $entity_token
            );

            /**
             *
             * @var \Application\Entity\NmtApplicationAttachment $entity ;
             */
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findOneBy($criteria);

            if (! $entity instanceof \Application\Entity\NmtApplicationAttachment) {

                $errors[] = 'Entity object can\'t be empty!';
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
                    
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'target' => null,
                    'entity' => null,
                    'n' => $nTry
                    
                ));
                
                $viewModel->setTemplate("procure/pr-row-attachment/upload");
                return $viewModel;
                
            }
            
            $result = $this->getAttachmentService()->editHeader($entity, $data, $nTry, $u);
            
            $errors = $result['errors'];
            $nTry = $result['nTry'];
            
            if ($nTry >= 3) {
                $errors[] = sprintf('Do you really want to edit "Attachment. %s"?', $entity->getId());
            }
            
            if ($nTry == 5) {
                $m = sprintf('You might be not ready to edit Attachment (%s). Please try later!', $entity->getId());
                $this->flashMessenger()->addMessage($m);
                return $this->redirect()->toUrl($redirectUrl);
            }
            
            
            if (count($errors) > 0) {
                
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,                    
                    'redirectUrl' => $redirectUrl,
                    'target' => null,
                    'errors' => $errors,
                    'entity' => $entity,
                    'n' => $nTry
                ));
                
                $viewModel->setTemplate("procure/pr-row-attachment/upload");
                return $viewModel;
            }
            
            $m = sprintf('[OK] Attachment #%s updated. Change No.=%s.', $entity->getId(),'');
            $this->flashMessenger()->addMessage($m);
            
            return $this->redirect()->toUrl($redirectUrl);            
            
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
              
            $viewModel = new ViewModel(array(
                'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'target' => null,
                'entity' => $entity,
                'n' => 0
            ));
            
            $viewModel->setTemplate("procure/pr-row-attachment/upload");
            return $viewModel;
            
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
                //'isActive' => 1,
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
        $entity_id = (int) $this->params()->fromQuery('id');
        $entity_checksum = $this->params()->fromQuery('checksum');
        $entity_token = $this->params()->fromQuery('token');

        if ($entity_token == '') {
            $entity_token = null;
        }

        $result = $this->getAttachmentService()->getPicture($entity_id, $entity_checksum, $entity_token);

        if ($result !== null) {

            $response = $this->getResponse();
            $response->setContent($result['imageContent']);
            $response->getHeaders()
                ->addHeaderLine('Content-Transfer-Encoding', 'binary')
                ->addHeaderLine('Content-Type', $result['fileType']);

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
        $entity_id = (int) $this->params()->fromQuery('id');
        $entity_checksum = $this->params()->fromQuery('checksum');
        $entity_token = $this->params()->fromQuery('token');

        if ($entity_token == '') {
            $entity_token = null;
        }

        $result = $this->getAttachmentService()->getThumbnail200($entity_id, $entity_checksum, $entity_token);

        if ($result !== null) {

            $response = $this->getResponse();
            $response->setContent($result['imageContent']);
            $response->getHeaders()
                ->addHeaderLine('Content-Transfer-Encoding', 'binary')
                ->addHeaderLine('Content-Type', $result['fileType']);

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
        $entity_id = (int) $this->params()->fromQuery('id');
        $entity_checksum = $this->params()->fromQuery('checksum');
        $entity_token = $this->params()->fromQuery('token');

        if ($entity_token == '') {
            $entity_token = null;
        }

        $result = $this->getAttachmentService()->getThumbnail450($entity_id, $entity_checksum, $entity_token);

        if ($result !== null) {

            $response = $this->getResponse();
            $response->setContent($result['imageContent']);
            $response->getHeaders()
                ->addHeaderLine('Content-Transfer-Encoding', 'binary')
                ->addHeaderLine('Content-Type', $result['fileType']);

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
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'target' => null,
                    'entity' => null
                ));

                $viewModel->setTemplate("procure/pr-row-attachment/upload");
                return $viewModel;
            }

            $attachments = null;
            if (isset($_FILES['attachments'])) {
                $attachments = $_FILES['attachments'];
            }

            $errors = $this->getAttachmentService()->doUploading(null, $target_id, $target_token, $data, $attachments, $u, TRUE);

            if (count($errors) > 0) {
                $this->flashMessenger()->addMessage('Something wrong!');
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'target' => $target,
                    'entity' => $this->getAttachmentService()->getAttachmentEntity()
                ));

                $viewModel->setTemplate("procure/pr-row-attachment/upload");
                return $viewModel;
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

            $viewModel = new ViewModel(array(
                'action' => \Application\Model\Constants::FORM_ACTION_ADD,
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'target' => $target,
                'entity' => null
            ));

            $viewModel->setTemplate("procure/pr-row-attachment/upload");
            return $viewModel;
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
            $data = $this->getAttachmentService()->doUploadPictures($postArray, $u);

            if (isset($data['message'])) {
                foreach ($data['message'] as $m) {
                    $this->flashMessenger()->addMessage($m);
                }
            }

            $this->flashMessenger()->addMessage('Success: ' . $data['success'] . '/' . $data['total_uploaded']);
            $this->flashMessenger()->addMessage('Failed: ' . $data['failed'] . '/' . $data['total_uploaded']);

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
    
    /**
     *
     * @return \Procure\Service\Upload\PrRowUploadService
     */
    public function getAttachmentService()
    {
        return $this->attachmentService;
    }
    
    /**
     *
     * @param \Procure\Service\Upload\PrRowUploadService $attachmentService
     */
    public function setAttachmentService(\Procure\Service\Upload\PrRowUploadService $attachmentService)
    {
        $this->attachmentService = $attachmentService;
    }
    
}
