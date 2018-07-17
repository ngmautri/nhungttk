<?php
namespace Procure\Controller;

use Application\Service\PdfService;
use Doctrine\ORM\EntityManager;
use Zend\Mail\Message;
use Zend\Mail\Header\ContentType;
use Zend\Mail\Transport\Smtp as smtptransport;
use Zend\Mime\Message as mimemessage;
use Zend\Mime\Part as mimepart;
use Zend\Mvc\Controller\AbstractActionController;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrConsoleController extends AbstractActionController
{

    const BACKUP_FOLDER = "/data/back-up/db";

    protected $doctrineEM;

    protected $pdfService;

    protected $outlookEmailService;

    protected $smtpTransportService;

    /**
     * 
     * {@inheritDoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {}

    /**
     *
     * php c:\1-nmt\1-eclipse\workspace\mla-03\public\index.php validate
     *
     * @return \zend\stdlib\responseinterface|\zend\view\model\viewmodel
     */
    public function validate1Action()
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
     * php c:\1-nmt\1-eclipse\workspace\mla-02-01\public\index.php validate
     * create enviroment varibles: C:\Program Files\MySQL\MySQL Server 5.7\bin
     *
     * @return \zend\stdlib\responseinterface|\zend\view\model\viewmodel
     */
    public function validateAction()
    {
        $request = $this->getRequest();
        
        // make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (! $request instanceof \Zend\Console\Request) {
            throw new \RuntimeException('you can only use this action from a console-- nmt!');
        }
        
        // exec ( 'java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar encrypt -o mla2017 -u ' . $filepassword . ' ' . "$folder/$name" );
        // exec('mysqldump --user=... --password=... --host=... db_name > /path/to/output/file.sql');
        
        $filename = ROOT . self::BACKUP_FOLDER . '/sql_' . date("m-d-Y");
        
        // exec ( 'java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar encrypt -o mla2017 -u ' . $filepassword . ' ' . "$folder/$name" );
        exec('mysqldump -u root --password=NMTerfolgkflg#7986 mla --result-file ' . $filename . '.sql');
        
        // abtractcontroller is eventmanageraware.
        $this->getEventmanager()->trigger('system.log', __class__, array(
            'priority' => 7,
            'message' => '[ok] database backed up automatically!'
        ));
    }

    // setter and getter

    /**
     * @return mixed
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     * @return mixed
     */
    public function getPdfService()
    {
        return $this->pdfService;
    }

    /**
     * @return mixed
     */
    public function getOutlookEmailService()
    {
        return $this->outlookEmailService;
    }

    /**
     * @return mixed
     */
    public function getSmtpTransportService()
    {
        return $this->smtpTransportService;
    }

    /**
     * @param mixed $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }

    /**
     * @param mixed $pdfService
     */
    public function setPdfService($pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * @param mixed $outlookEmailService
     */
    public function setOutlookEmailService($outlookEmailService)
    {
        $this->outlookEmailService = $outlookEmailService;
    }

    /**
     * @param mixed $smtpTransportService
     */
    public function setSmtpTransportService($smtpTransportService)
    {
        $this->smtpTransportService = $smtpTransportService;
    }
    
}
