<?php
namespace Procure\API;

use Doctrine\ORM\EntityManager;
use Procure\API\Contracts\ProcureRestController;
use Zend\View\Model\JsonModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrController extends ProcureRestController
{

    protected $doctrineEM;

    /**
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractRestfulController::get()
     */
    public function get($id)
    {
        $a_json_final = array();
        $a_json_final['data'] = __METHOD__ . ' NMT get current data with id =  ' . $id;

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($a_json_final));

        return $response;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractRestfulController::getList()
     */
    public function getList()
    {
        $response = $this->getResponseWithHeader()->setContent(__METHOD__ . ' hahahah get the list of data');
        return $response;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractRestfulController::create()
     */
    public function create($data)
    {
        $f = "%s create new item of data : %s";

        $this->logInfo(json_encode($data));
        // $this->$response = $this->getResponseWithHeader()->setContent(sprintf($f, __METHOD__, json_encode($data)));
        // return $response;

        return new JsonModel(array(
            'data' => $data
        ));
    }

    public function update($id, $data)
    {
        $response = $this->getResponseWithHeader()->setContent(__METHOD__ . ' update current data with id =  ' . $id . ' with data of name is ' . $data['name']);
        return $response;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractRestfulController::delete()
     */
    public function delete($id)
    {
        $response = $this->getResponseWithHeader()->setContent(__METHOD__ . ' delete current data with id =  ' . $id);
        return $response;
    }

    /**
     * configure response
     */
    public function getResponseWithHeader()
    {
        $response = $this->getResponse();
        $response->getHeaders()
            ->
        // make can accessed by *
        addHeaderLine('Access-Control-Allow-Origin', '*')
            ->
        // set allow methods
        addHeaderLine('Access-Control-Allow-Methods', 'POST PUT DELETE GET');

        return $response;
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
     * @return \PM\Controller\IndexController
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }
}
