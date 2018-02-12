<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Procure\Controller;

use Zend\Validator\EmailAddress;
use Zend\Mail\Message;
use Zend\View\Model\ViewModel;
use Zend\Http\Headers;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\Mail\Header\ContentType;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Application\Service\PdfService;

class PrConsoleController extends AbstractActionController
{
    const BACKUP_FOLDER = "/data/back-up/db";
    

    protected $doctrineEM;

    protected $pdfService;

    protected $outlookEmailService;

    protected $SmtpTransportService;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

    /**
     *
     *php C:\1-NMT\1-Eclipse\Workspace\mla-02\public\index.php validate
     * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
     */
    public function validate1Action()
    {
        
        //exec ( 'java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar Encrypt -O mla2017 -U ' . $filePassword . ' ' . "$folder/$name" );
        //exec('mysqldump --user=... --password=... --host=... DB_NAME > /path/to/output/file.sql');
        
        $fileName = ROOT.self::BACKUP_FOLDER.'/sql_' . date ("m-d-Y");
        echo $fileName;
        
        //exec ( 'java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar Encrypt -O mla2017 -U ' . $filePassword . ' ' . "$folder/$name" );
        exec('mysqldump -u root --password=kflg79 mla --result-file=' . $fileName . '.sql');
        
        
        $request = $this->getRequest();
        
        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (! $request instanceof \Zend\Console\Request) {
            throw new \RuntimeException('You can only use this action from a console-- NMT!');
        }
        
        $transport = $this->getOutlookEmailService();
        
        $emailText = <<<EOT
		
<p>Hello Sparepart Controller,</p>

EOT;
        
        echo 'sent!';
        
        
        $html = new MimePart($emailText);
        $html->type = "text/html";
        
        $body = new MimeMessage();
        $body->setParts(array(
            $html
        ));
        
        // build message
        $message = new Message();
        $message->addFrom('mla-web@outlook.com');
        $message->addTo("nmt@mascot.dk");
        $message->addCc("nmt@mascot.dk");
        $message->setSubject('MLA - Sparepart Order Sugguestion - ' . date("m-d-Y"));
        
        $type = new ContentType();
        $type->setType('text/html');
        
        $message->getHeaders()->addHeader($type);
        $message->setBody("xin chao chu em");
        $message->setEncoding("UTF-8");
        
          
        // send message
        $transport->send($message);
       
    }
    
    /**
     *
     *php C:\1-NMT\1-Eclipse\Workspace\mla-02\public\index.php validate
     * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
     */
    public function validateAction()
    {
        
        
        $request = $this->getRequest();
        
        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (! $request instanceof \Zend\Console\Request) {
            throw new \RuntimeException('You can only use this action from a console-- NMT!');
        }
        
        //exec ( 'java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar Encrypt -O mla2017 -U ' . $filePassword . ' ' . "$folder/$name" );
        //exec('mysqldump --user=... --password=... --host=... DB_NAME > /path/to/output/file.sql');
        
        $fileName = ROOT.self::BACKUP_FOLDER.'/sql_' . date ("m-d-Y");
        
        //exec ( 'java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar Encrypt -O mla2017 -U ' . $filePassword . ' ' . "$folder/$name" );
        exec('mysqldump -u root --password=kflg79 mla --result-file=' . $fileName . '.sql');
   
        //AbtractController is EventManagerAware.
        $this->getEventManager()->trigger('system.log', __CLASS__, array(
            'priority' => 7,
            'message' => 'Database backed up automatically!'
        ));
        
        
        
        $transport = $this->getOutlookEmailService();
        
        $emailText = <<<EOT
        
<p>Hello Sparepart Controller,</p>
<p>Below is the sugguestion for ordering of spare parts!</p>
<p> Please click <a href="http://laosit02/">http://laosit02/</a> for more detail!</p>
<p>
Regards,<br/>
MLA Team
</p>
<p>(<em>This Email is generated by the system automatically. Please do not reply!</em>)</p>
EOT;
        
        $html = new MimePart($emailText);
        $html->type = "text/html";
        
        $body = new MimeMessage();
        $body->setParts(array(
            $html
        ));
        
        // build message
        $message = new Message();
        $message->addFrom('mla-app@outlook.com');
        $message->addTo("nmt@mascot.dk");
        $message->addCc("nmt@mascot.dk");
        $message->setSubject('MLA - Sparepart Order Sugguestion - ' . date("m-d-Y"));
        
        $type = new ContentType();
        $type->setType('text/html');
        
        $message->getHeaders()->addHeader($type);
        $message->setBody("Xin Chao chu em");
        $message->setEncoding("UTF-8");
        
        
        // send message
        $transport->send($message);
        
        //AbtractController is EventManagerAware.
        $this->getEventManager()->trigger('system.log', __CLASS__, array(
            'priority' => 7,
            'message' => 'email sent!'
        ));
        
        
    }

    // SETTER AND GETTER
    
    /**
     *
     * @return mixed
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @return mixed
     */
    public function getPdfService()
    {
        return $this->pdfService;
    }

    /**
     *
     * @param mixed $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }

    /**
     *
     * @param mixed $pdfService
     */
    public function setPdfService(PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     *
     * @return mixed
     */
    public function getOutlookEmailService()
    {
        return $this->outlookEmailService;
    }

    /**
     *
     * @return mixed
     */
    public function getSmtpTransportService()
    {
        return $this->SmtpTransportService;
    }

    /**
     *
     * @param mixed $outlookEmailService
     */
    public function setOutlookEmailService(SmtpTransport $outlookEmailService)
    {
        $this->outlookEmailService = $outlookEmailService;
    }

    /**
     *
     * @param mixed $SmtpTransportService
     */
    public function setSmtpTransportService($SmtpTransportService)
    {
        $this->SmtpTransportService = $SmtpTransportService;
    }
}
