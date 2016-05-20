<?php
namespace Inventory\Services;


use Zend\Http\Request;
use Zend\Http\Client;
use Zend\Stdlib\Parameters;



/*
 * @author nmt
 *
 */
class EpartnerService
{
	public function get()
	{
		$request = new Request();
		$request->getHeaders()->addHeaders(array(
				'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
		));
		//$request->setUri("https://mascot.pensio.com/merchant/API/payments?shop_orderid=17700000014");
		//$request->setMethod('get');
		$userName  = "pensio_api@mascot.dk";
		$passWord = "CFKUUKPvdEbK";
				
		//$request->setPost(new Parameters(array($userName,$passWord)));
		
		//$ssl_path = "C:\xampp\php\pensio.com.txt";
		//echo $ssl_path;
		
		$client = new Client('https://mascot.pensio.com/merchant/API/payments?shop_orderid=17700000014');
		
		/*
		$client = new Client('https://mascot.pensio.com/merchant/API/payments?shop_orderid=17700000014',
				array('adapter' => 'Zend\Http\Client\Adapter\Curl'
		));
		*/
		$adapter = new \Zend\Http\Client\Adapter\Curl();
		$adapter->setCurlOption(CURLOPT_SSL_VERIFYHOST,false);
		$adapter->setCurlOption(CURLOPT_SSL_VERIFYPEER,false);
		$client->setAdapter($adapter);
		$client->setAuth($userName,$passWord);
			
		$response = $client->send();
		
		if ($response->isSuccess()) {
			// the POST was successful
			$data = simplexml_load_string($response->getBody());
			return $data->Body[0]->Transactions;
		}else {
			return "the POST was unsuccessful";
			
		}
		
	}

}