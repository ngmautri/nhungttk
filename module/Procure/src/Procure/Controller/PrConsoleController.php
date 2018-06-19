<?php

/**
 * zend framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zendskeletonapplication for the canonical source repository
 * @copyright copyright (c) 2005-2013 zend technologies usa inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd new bsd license
 */
namespace procure\controller;

use zend\validator\emailaddress;
use zend\mail\message;
use zend\view\model\viewmodel;
use zend\http\headers;
use zend\mail\transport\smtp as smtptransport;
use zend\mime\part as mimepart;
use zend\mime\message as mimemessage;
use zend\mail\header\contenttype;
use zend\mvc\controller\abstractactioncontroller;
use doctrine\orm\entitymanager;
use application\service\pdfservice;

class prconsolecontroller extends abstractactioncontroller
{

    const backup_folder = "/data/back-up/db";

    protected $doctrineem;

    protected $pdfservice;

    protected $outlookemailservice;

    protected $smtptransportservice;

    /*
     * defaul action
     */
    public function indexaction()
    {}

    /**
     *
     * php c:\1-nmt\1-eclipse\workspace\mla-03\public\index.php validate
     *
     * @return \zend\stdlib\responseinterface|\zend\view\model\viewmodel
     */
    public function validate1action()
    {
        
        // exec ( 'java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar encrypt -o mla2017 -u ' . $filepassword . ' ' . "$folder/$name" );
        // exec('mysqldump --user=... --password=... --host=... db_name > /path/to/output/file.sql');
        $filename = root . self::backup_folder . '/sql_' . date("m-d-y");
        echo $filename;
        
        // exec ( 'java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar encrypt -o mla2017 -u ' . $filepassword . ' ' . "$folder/$name" );
        exec('mysqldump -u root --password=kflg79 mla --result-file=' . $filename . '.sql');
        
        $request = $this->getrequest();
        
        // make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (! $request instanceof \zend\console\request) {
            throw new \runtimeexception('you can only use this action from a console-- nmt!');
        }
        
        $transport = $this->getoutlookemailservice();
        
        $emailtext = <<<eot
		
<p>hello sparepart controller,</p>

eot;
        
        echo 'sent!';
        
        $html = new mimepart($emailtext);
        $html->type = "text/html";
        
        $body = new mimemessage();
        $body->setparts(array(
            $html
        ));
        
        // build message
        $message = new message();
        $message->addfrom('mla-web@outlook.com');
        $message->addto("nmt@mascot.dk");
        $message->addcc("nmt@mascot.dk");
        $message->setsubject('mla - sparepart order sugguestion - ' . date("m-d-y"));
        
        $type = new contenttype();
        $type->settype('text/html');
        
        $message->getheaders()->addheader($type);
        $message->setbody("xin chao chu em");
        $message->setencoding("utf-8");
        
        // send message
        $transport->send($message);
    }

    /**
     *
     * php c:\1-nmt\1-eclipse\workspace\mla-03\public\index.php validate
     *
     * @return \zend\stdlib\responseinterface|\zend\view\model\viewmodel
     */
    public function validateaction()
    {
        $request = $this->getrequest();
        
        // make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (! $request instanceof \zend\console\request) {
            throw new \runtimeexception('you can only use this action from a console-- nmt!');
        }
        
        // exec ( 'java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar encrypt -o mla2017 -u ' . $filepassword . ' ' . "$folder/$name" );
        // exec('mysqldump --user=... --password=... --host=... db_name > /path/to/output/file.sql');
        
        $filename = root . self::backup_folder . '/sql_' . date("m-d-y");
        
        // exec ( 'java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar encrypt -o mla2017 -u ' . $filepassword . ' ' . "$folder/$name" );
        exec('mysqldump -u root --password=kflg7986 mla --result-file ' . $filename . '.sql');
        
        // abtractcontroller is eventmanageraware.
        $this->geteventmanager()->trigger('system.log', __class__, array(
            'priority' => 7,
            'message' => '[ok] database backed up automatically!'
        ));
    }

    // setter and getter
    
    /**
     *
     * @return mixed
     */
    public function getdoctrineem()
    {
        return $this->doctrineem;
    }

    /**
     *
     * @return mixed
     */
    public function getpdfservice()
    {
        return $this->pdfservice;
    }

    /**
     *
     * @param mixed $doctrineem
     */
    public function setdoctrineem(entitymanager $doctrineem)
    {
        $this->doctrineem = $doctrineem;
    }

    /**
     *
     * @param mixed $pdfservice
     */
    public function setpdfservice(pdfservice $pdfservice)
    {
        $this->pdfservice = $pdfservice;
    }

    /**
     *
     * @return mixed
     */
    public function getoutlookemailservice()
    {
        return $this->outlookemailservice;
    }

    /**
     *
     * @return mixed
     */
    public function getsmtptransportservice()
    {
        return $this->smtptransportservice;
    }

    /**
     *
     * @param mixed $outlookemailservice
     */
    public function setoutlookemailservice(smtptransport $outlookemailservice)
    {
        $this->outlookemailservice = $outlookemailservice;
    }

    /**
     *
     * @param mixed $smtptransportservice
     */
    public function setsmtptransportservice($smtptransportservice)
    {
        $this->smtptransportservice = $smtptransportservice;
    }
}
