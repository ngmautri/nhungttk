<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Inventory\Controller;

use Zend\I18n\Validator\Int;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\Validator\EmailAddress;
use Zend\Mail\Message;
use Zend\View\Model\ViewModel;

use MLA\Paginator;
use MLA\Files;


use Inventory\Model\Article;
use Inventory\Model\ArticleTable;

use Inventory\Services\ArticleService;
use Inventory\Services\ArticleSearchService;


use Inventory\Model\ArticlePicture;
use Inventory\Model\ArticlePictureTable;

use Inventory\Model\ArticleCategory;
use Inventory\Model\ArticleCategoryTable;

use Inventory\Model\ArticleCategoryMember;
use Inventory\Model\ArticleCategoryMemberTable;

use Inventory\Model\ArticleMovement;
use Inventory\Model\ArticleMovementTable;
use Procurement\Model\PurchaseRequestCartItem;
use Procurement\Model\PurchaseRequestCartItemTable;
use Procurement\Model\PurchaseRequestItemTable;

class ArticleController extends AbstractActionController {
	protected $SmtpTransportService;
	protected $authService;
	protected $articleService;
	protected $articleSearchService;
	
	
	
	protected $userTable;	
	protected $articleTable;
	protected $articleCategoryTable;
	protected $articleCategoryMemberTable;
	protected $articlePictureTable;
	protected $articleMovementTable;
	protected  $purchaseRequestCartItemTable;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	
	/**
	 * create New Article
	 */
	public function addAction() {
		
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity();
		$user=$this->userTable->getUserByEmail($identity);
		
		if ($request->isPost ()) {
			
			if ($request->isPost ()) {
				$redirectUrl  = $request->getPost ( 'redirectUrl' );
				
				$input = new Article();
				$input->name = $request->getPost ( 'name' );
				$input->name_local = $request->getPost ( 'name_local' );
				
				$input->description = $request->getPost ( 'description' );
				
				$input->keywords = $request->getPost ( 'keywords' );
				$input->unit = $request->getPost ( 'unit' );
				
				$input->type = $request->getPost ( 'type' );
				$input->code = $request->getPost ( 'code' );
				
				$input->barcode = $request->getPost ( 'barcode' );
				
				$input->created_by =  $user['id'];
				
				$input->visibility = $request->getPost ( 'visibility' );
				$input->status = $request->getPost ( 'status' );
				$input->remarks = $request->getPost ( 'remarks' );
				
				
				$category_id = (int) $request->getPost ( 'category_id' );
				
								
				$errors = array();
		
				if ($input->name ==''){
					$errors [] = 'Please give article name';
				}
					
				
				if (count ( $errors ) > 0) {
					return new ViewModel ( array (
							'errors' => $errors,
							'redirectUrl'=>$redirectUrl,
							'category_id' => $category_id,
							'article' =>$input,							
					) );
				}
				
				
				$newId = $this->articleTable->add ( $input );
				$row = $this->articleTable->getArticleByID($newId);
				$this->articleSearchService->updateIndex($row);
				
				//update search index
				
				
				$root_dir = $this->articleService->getPicturesPath();
				
				// $files = $request->getFiles ()->toArray ();
				
				$pictureUploadListener = $this->getServiceLocator()->get ( 'Inventory\Listener\PictureUploadListener');
				$this->getEventManager()->attachAggregate ( $pictureUploadListener );
				
				$id = $newId;
				
				foreach ( $_FILES ["pictures"] ["error"] as $key => $error ) {
					if ($error == UPLOAD_ERR_OK) {
						$tmp_name = $_FILES ["pictures"] ["tmp_name"] [$key];
				
						$ext = strtolower (pathinfo($_FILES ["pictures"] ["name"] [$key], PATHINFO_EXTENSION));
				
						if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'gif' || $ext == 'png'){
								
							$checksum = md5_file($tmp_name);
								
							if(!$this->articlePictureTable->isChecksumExits($id, $checksum)){
									
								$name = md5($id.$checksum.uniqid(microtime())).'.'. $ext;
								$folder = $root_dir.DIRECTORY_SEPARATOR. $name[0].$name[1].DIRECTORY_SEPARATOR.$name[2].$name[3].DIRECTORY_SEPARATOR.$name[4].$name[5];
				
								if (!is_dir ( $folder)) {
									mkdir ($folder,0777, true ); //important
								}
									
								$ftype = $_FILES ["pictures"] ["type"] [$key];
								move_uploaded_file ( $tmp_name, "$folder/$name" );
				
								// add pictures
								$pic = new ArticlePicture();
								$pic->url = "$folder/$name";
								$pic->filetype = $ftype;
								$pic->article_id = $id;
								$pic->filename = "$name";
								$pic->folder = "$folder";
								$pic->checksum = $checksum;
				
								$this->articlePictureTable->add ( $pic);
				
								// trigger uploadPicture
								$this->getEventManager()->trigger('uploadPicture', __CLASS__,
										array('picture_name' => $name,'pictures_dir'=>$folder));
									
							}
								
						}
							
					}
				}
				
				

				
				// add category
				if ($category_id > 1){
					$m = new ArticleCategoryMember();
					$m->article_id = $newId;
					$m->article_cat_id = $category_id;						
					$this->articleCategoryMemberTable->add($m);					
				}
								
				$this->redirect()->toUrl($redirectUrl);
			}
		}
		
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		$category_id = ( int ) $this->params ()->fromQuery ( 'category_id' );
		
		// add category
		if ($category_id > 1){
			$category = $this->articleCategoryTable->get($category_id);
		}else{
			$category = null;
		}
		
			
		return new ViewModel ( array (
				'message' => 'Add new article',
				'category' => $category,
				'redirectUrl'=>$redirectUrl,
				'errors' => null,
				'article' =>null,
			) );
	}
	
	/**
	 * Show Movement of Article
	 */
	public function movementsAction() {
		$id = ( int ) $this->params ()->fromQuery ( 'article_id' );
		$movements = $this->articleMovementTable->getMovements($id);
		$article = $this->articleTable->getArticleByID( $id );
		
		return new ViewModel ( array (
				'article' => $article,
				'movements' => $movements,
		) );
	}
	
	
	/**
	 * Issue Article
	 */
	public function issueAction() {
		
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		
		if ($request->isPost ()) {
	
			$input = new ArticleMovement();
			$input->article_id = $request->getPost ( 'article_id' );
			$input->movement_date = $request->getPost ( 'movement_date' );
			$input->quantity = $request->getPost ( 'quantity' );
			$input->flow = 'OUT';
			$input->reason = $request->getPost ( 'reason' );
			$input->requester = $request->getPost ( 'requester' );
			$input->comment = $request->getPost ( 'comment' );
			$input->created_on = $request->getPost ( 'created_on' );
	
			$instock = $request->getPost ( 'instock' );
			$redirectUrl  = $request->getPost ( 'redirectUrl' );
				
	
	
			// validator.
			$validator = new Date ();
	
			$errors = array();
	
	
			if (! $validator->isValid ( $input->movement_date )) {
				$errors [] = 'Transaction date format is not correct!';
			}
	
			// Fixed it by going to php.ini and uncommenting extension=php_intl.dll
			$validator = new Int ();
	
			if (! $validator->isValid ( $input->quantity )) {
				$errors [] = 'Quantity is not valid. It must be a number.';
			} else {
	
				if ($input->quantity > $instock) {
					$errors [] = 'Issue quantity is: ' . $input->quantity . ' pcs, which is bigger than availabe stock';
				}
			}
	
			if (count ( $errors ) > 0) {
	
				$id = ( int ) $request->getPost ( 'article_id' );
				$article = $this->articleTable->getArticleByID( $id );
				$pictures = $this->articlePictureTable->getArticlePicturesById( $id );
	
				return new ViewModel ( array (
						'article' => $article,
						'pictures' => $pictures,
						'instock' => $instock,
						'errors' => $errors,
						'redirectUrl'=>$redirectUrl,
						'movement'=>$input,
				) );
			} else {
	
				// Validated
				$this->articleMovementTable->add ( $input );
				$this->redirect()->toUrl($redirectUrl);
			}
		}
	
		$id = ( int ) $this->params ()->fromQuery ( 'article_id' );
		$article = $this->articleTable->getArticleByID( $id );
		$pictures = $this->articlePictureTable->getArticlePicturesById( $id );
		$instock = $article->article_balance;
	
		return new ViewModel ( array (
				'article' => $article,
				'pictures' => $pictures,
				'instock' => $instock,
				'errors' => null,
				'redirectUrl'=>$redirectUrl,
				'movement'=>null,
		) );
	}
	
	
	/**
	 * Edit Spare part
	 * @return \Zend\View\Model\ViewModel
	 */
	
	public function editAction() {
		$request = $this->getRequest ();
	
		if ($request->isPost ()) {
			
			$id = $request->getPost ( 'id' );
				
			$input = new Article();
			$input->name = $request->getPost ( 'name' );
			$input->name_local = $request->getPost ( 'name_local' );
			$input->description = $request->getPost ( 'description' );
			$input->keywords = $request->getPost ( 'keywords' );
			$input->unit = $request->getPost ( 'unit' );
			$input->type = $request->getPost ( 'type' );
			$input->code = $request->getPost ( 'code' );
			$input->barcode = $request->getPost ( 'barcode' );
			//$input->created_by =  $user['id'];
			
			$input->visibility = $request->getPost ( 'visibility' );
			$input->status = $request->getPost ( 'status' );
			$input->remarks = $request->getPost ( 'remarks' );
			
			$errors = array();
			
			if ($input->name ==''){
				$errors [] = 'Please give spare-part name';
			}
			
			$redirectUrl  = $request->getPost ( 'redirectUrl' );
			
			
			if (count ( $errors ) > 0) {
				// return current sp
				$article = $this->articleTable->get ( $input->id );
				
				return new ViewModel ( array (
						'errors' => $errors,
						'redirectUrl'=>$redirectUrl,
						'article' =>$article,
				) );
			}
				
			$this->articleTable->update ( $input, $id);
			$root_dir = $this->articleService->getPicturesPath();
			
			// $files = $request->getFiles ()->toArray ();
			
			$pictureUploadListener = $this->getServiceLocator()->get ( 'Inventory\Listener\PictureUploadListener');
			$this->getEventManager()->attachAggregate ( $pictureUploadListener );
			
			
			foreach ( $_FILES ["pictures"] ["error"] as $key => $error ) {
				if ($error == UPLOAD_ERR_OK) {
					$tmp_name = $_FILES ["pictures"] ["tmp_name"] [$key];
			
					$ext = strtolower (pathinfo($_FILES ["pictures"] ["name"] [$key], PATHINFO_EXTENSION));
			
					if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'gif' || $ext == 'png'){
			
						$checksum = md5_file($tmp_name);
			
						if(!$this->articlePictureTable->isChecksumExits($id, $checksum)){
								
							$name = md5($id.$checksum.uniqid(microtime())).'.'. $ext;
							$folder = $root_dir.DIRECTORY_SEPARATOR. $name[0].$name[1].DIRECTORY_SEPARATOR.$name[2].$name[3].DIRECTORY_SEPARATOR.$name[4].$name[5];
			
							if (!is_dir ( $folder)) {
								mkdir ($folder,0777, true ); //important
							}
								
							$ftype = $_FILES ["pictures"] ["type"] [$key];
							move_uploaded_file ( $tmp_name, "$folder/$name" );
			
							// add pictures
							$pic = new ArticlePicture();
							$pic->url = "$folder/$name";
							$pic->filetype = $ftype;
							$pic->article_id = $id;
							$pic->filename = "$name";
							$pic->folder = "$folder";
							$pic->checksum = $checksum;
			
							$this->articlePictureTable->add ( $pic);
			
							// trigger uploadPicture
							$this->getEventManager()->trigger('uploadPicture', __CLASS__,
									array('picture_name' => $name,'pictures_dir'=>$folder));
								
						}
					}
				}
			}
			
			$this->redirect()->toUrl($redirectUrl);
		}
	
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$article = $this->articleTable->get ( $id );
	
		return new ViewModel ( array (
				'article' => $article,
				'redirectUrl'=>$redirectUrl,
				'errors' => null,
		) );
	}
	
	/**
	 * Edit Spare part
	 * @return \Zend\View\Model\ViewModel
	 */
	
	public function uploadPictureAction() {
		$request = $this->getRequest ();
			
	
		if ($request->isPost ()) {
	
			$id = $request->getPost ( 'id' );
			$root_dir = $this->sparePartService->getPicturesPath();
		
			// $files = $request->getFiles ()->toArray ();
	
			$pictureUploadListener = $this->getServiceLocator()->get ( 'Inventory\Listener\PictureUploadListener');
			$this->getEventManager()->attachAggregate ( $pictureUploadListener );
	
	
			foreach ( $_FILES ["pictures"] ["error"] as $key => $error ) {
				if ($error == UPLOAD_ERR_OK) {
					$tmp_name = $_FILES ["pictures"] ["tmp_name"] [$key];
					
					$ext = strtolower (pathinfo($_FILES ["pictures"] ["name"] [$key], PATHINFO_EXTENSION));
					
					if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'gif' || $ext == 'png'){
						
						$checksum = md5_file($tmp_name);
						
						if(!$this->sparePartPictureTable->isChecksumExits($id, $checksum)){
						
							$name = md5($id.$checksum.uniqid(microtime())).'.'. $ext;
							$folder = $root_dir.DIRECTORY_SEPARATOR. $name[0].$name[1].DIRECTORY_SEPARATOR.$name[2].$name[3].DIRECTORY_SEPARATOR.$name[4].$name[5];
							
							if (!is_dir ( $folder)) {
							 	mkdir ($folder,0777, true ); //important
							}
				
							$ftype = $_FILES ["pictures"] ["type"] [$key];
							move_uploaded_file ( $tmp_name, "$folder/$name" );
			
							// add pictures
							$pic = new SparepartPicture ();
							$pic->url = "$folder/$name";
							$pic->filetype = $ftype;
							$pic->sparepart_id = $id;
							$pic->filename = "$name";
							$pic->folder = "$folder";
							$pic->checksum = $checksum;
							
							$this->sparePartPictureTable->add ( $pic);
			
							// trigger uploadPicture
							$this->getEventManager()->trigger('uploadPicture', __CLASS__, 
									array('picture_name' => $name,'pictures_dir'=>$folder));
						
						}
						
					}
	
				}
			}
	
			$redirectUrl  = $request->getPost ( 'redirectUrl' );
			$this->redirect()->toUrl($redirectUrl);
	
			//return $this->redirect ()->toRoute ( 'Spareparts_Category');
		}
	
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$sparepart = $this->sparePartTable->get ( $id );
	
		return new ViewModel ( array (
				'sparepart' => $sparepart,
				'redirectUrl'=>$redirectUrl,
				'errors' => null,
		) );
	}
	
	/**
	 * Issue sparepart
	 */

	/**
	 * receive spare part
	 */
	public function receiveAction() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
	
	
		if ($request->isPost ()) {
				
			$request = $this->getRequest ();
				
			if ($request->isPost ()) {
	
				$input = new SparepartMovement ();
				$input->sparepart_id = $request->getPost ( 'sparepart_id' );
				$input->movement_date = $request->getPost ( 'movement_date' );
	
				$input->sparepart_id = $request->getPost ( 'sparepart_id' );
				$input->asset_id = $request->getPost ( 'asset_id' );
				$input->quantity = $request->getPost ( 'quantity' );
	
				$input->flow = 'IN';
	
				$input->reason = $request->getPost ( 'reason' );
				$input->requester = $request->getPost ( 'requester' );
				$input->comment = $request->getPost ( 'comment' );
				$input->created_on = $request->getPost ( 'created_on' );
				$email = $request->getPost ( 'email' );
	
				$instock = $request->getPost ( 'instock' );
				$redirectUrl  = $request->getPost ( 'redirectUrl' );
				
	
				$errors = array();
	
	
				// validator.
				$validator = new Date ();
	
				if (! $validator->isValid ( $input->movement_date )) {
					$errors [] = 'Transaction date format is not correct!';
				}
	
				// Fixed it by going to php.ini and uncommenting extension=php_intl.dll
				$validator = new Int ();
	
				if (! $validator->isValid ( $input->quantity )) {
					$errors [] = 'Quantity is not valid. It must be a number.';
				}
	
				$validator = new EmailAddress ();
				if (! $validator->isValid ( $email )) {
					$errors [] = 'Email is not correct.';
				}
	
				$id = ( int ) $request->getPost ( 'sparepart_id' );
				$sp = $this->sparePartTable->get ( $id );
				$pictures = $this->sparePartPictureTable->getSparepartPicturesById ( $id );
	
				if (count ( $errors ) > 0) {
					return new ViewModel ( array (
							'sp' => $sp,
							'pictures' => $pictures,
							'instock' => $instock,
							'errors' => $errors,
							'redirectUrl'=>$redirectUrl,								
							'movement'=>$input,
								
					) );
				} else {
						
					// Validated
					$newId = $this->sparepartMovementsTable->add ( $input );
					
					/* do not send email 
					if ($newId > 0) {
						// sent email;
	
						$transport = $this->getServiceLocator ()->get ( 'SmtpTransportService' );
						$message = new Message ();
						$body = $input->quantity . ' pcs of Spare parts ' . $sp->name . ' (ID' . $sp->tag . ') received!';
						$message->addTo ( $email )->addFrom ( 'mib-team@web.de' )->setSubject ( 'Mascot Laos - Spare Part Movements' )->setBody ( $body );
						$transport->send ( $message );
					}
					*/
						
						
					$redirectUrl  = $request->getPost ( 'redirectUrl' );
					$this->redirect()->toUrl($redirectUrl);
				}
			}
		}
	
		$id = ( int ) $this->params ()->fromQuery ( 'sparepart_id' );
		$sp = $this->sparePartTable->get ( $id );
		$pictures = $this->sparePartPictureTable->getSparepartPicturesById ( $id );
		$inflow = $this->sparepartMovementsTable->getTotalInflowOf ( $id );
		$outflow = $this->sparepartMovementsTable->getTotalOutflowOf ( $id );
		$instock = $inflow - $outflow;
	
		return new ViewModel ( array (
				'sp' => $sp,
				'pictures' => $pictures,
				'instock' => $instock,
				'errors' => null,
				'redirectUrl'=>$redirectUrl,
				'movement'=>null,
		) );
	}
	
	public function addCategoryAction() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		
		
		if ($request->isPost ()) {
			
			$sparepart_id = ( int ) $request->getPost ( 'id' );
			$categories = $request->getPost ( 'category' );
			
			if (count ( $categories ) > 0) {
				
				foreach ( $categories as $cat ) {
					$member = new SparepartCategoryMember ();
					$member->sparepart_cat_id = $cat;
					$member->sparepart_id = $sparepart_id;
					
					if ($this->sparePartCategoryMemberTable->isMember ( $sparepart_id, $cat ) == false) {
						$this->sparePartCategoryMemberTable->add ( $member );
					}
				}
				
				/*
				 * return new ViewModel ( array (
				 * 'sparepart' => null,
				 * 'categories' => $categories,
				 *
				 * ) );
				 */
				$redirectUrl  = $request->getPost ( 'redirectUrl' );
				$this->redirect()->toUrl($redirectUrl);
	
			}
		}
		
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$sparepart = $this->sparePartTable->get ( $id );
		
		$categories = $this->sparePartCategoryTable->getCategories();
		
		return new ViewModel ( array (
				'sparepart' => $sparepart,
				'categories' => $categories,
				'redirectUrl'=>$redirectUrl,
		)
		
		);
	}
	
	/**
	 * List all articles
	 */
	public function listAction() {
		$identity = $this->authService->getIdentity();
		$user=$this->userTable->getUserByEmail($identity);
		
		
		if (is_null ( $this->params ()->fromQuery ( 'perPage' ) )) {
			$resultsPerPage = 18;
		} else {
			$resultsPerPage = $this->params ()->fromQuery ( 'perPage' );
		}
		;
		
		if (is_null ( $this->params ()->fromQuery ( 'page' ) )) {
			$page = 1;
		} else {
			$page = $this->params ()->fromQuery ( 'page' );
		}
		;
		
		$identity = $this->authService->getIdentity();
		$user=$this->userTable->getUserByEmail($identity);
		$user_id  = $user['id'];
		
		
		$articles = $this->articleTable->getArticles($user_id, 0, 0);
		$totalResults = $articles->count ();
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$articles = $this->articleTable->getArticles($user_id,($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		$total_cart_items = $this->purchaseRequestCartItemTable->getTotalCartItems($user['id']);
		
		return new ViewModel ( array (
				'total_articles' => $totalResults,
				'articles' => $articles,
				'paginator' => $paginator,
				'total_cart_items' => $total_cart_items,
				
		) );
	}
	
	/**
	 * List all articles
	 */
	public function listWithLastDOAction() {
		if (is_null ( $this->params ()->fromQuery ( 'perPage' ) )) {
			$resultsPerPage = 10;
		} else {
			$resultsPerPage = $this->params ()->fromQuery ( 'perPage' );
		}
		;
	
		if (is_null ( $this->params ()->fromQuery ( 'page' ) )) {
			$page = 1;
		} else {
			$page = $this->params ()->fromQuery ( 'page' );
		}
		;
	
		$articles = $this->articleTable->getAllWithLastDO();
		$totalResults = $articles->count ();
	
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$articles = $this->articleTable->getAllWithLastDOWithLimit(($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
	
		return new ViewModel ( array (
				'total_articles' => $totalResults,
				'articles' => $articles,
				'paginator' => $paginator
		) );
	}
	
	/**
	 * List all articles
	 */
	public function myArticlesAction() {
		
		$identity = $this->authService->getIdentity();
		$user=$this->userTable->getUserByEmail($identity);
		$user_id  = $user['id'];
		
		if (is_null ( $this->params ()->fromQuery ( 'perPage' ) )) {
			$resultsPerPage = 2;
		} else {
			$resultsPerPage = $this->params ()->fromQuery ( 'perPage' );
		}
		;
	
		if (is_null ( $this->params ()->fromQuery ( 'page' ) )) {
			$page = 1;
		} else {
			$page = $this->params ()->fromQuery ( 'page' );
		}
		;
		
		
	
		$articles = $this->articleTable->getArticlesOf($user_id);
		$totalResults = $articles->count ();
	
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$articles = $this->articleTable->getLimittedArticlesOf($user_id,($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
	
		return new ViewModel ( array (
				'total_articles' => $totalResults,
				'articles' => $articles,
				'paginator' => $paginator
		) );
	}
	
	/**
	 * List all articles
	 */
	public function myDeptArticlesAction() {
	
		$identity = $this->authService->getIdentity();
		$user=$this->userTable->getUserByEmail($identity);
		$user_id  = $user['id'];
	
		if (is_null ( $this->params ()->fromQuery ( 'perPage' ) )) {
			$resultsPerPage = 20;
		} else {
			$resultsPerPage = $this->params ()->fromQuery ( 'perPage' );
		}
		;
	
		if (is_null ( $this->params ()->fromQuery ( 'page' ) )) {
			$page = 1;
		} else {
			$page = $this->params ()->fromQuery ( 'page' );
		}
		;
	
	
	
		$articles = $this->articleTable->getArticlesOfMyDepartment($user_id);
		$totalResults = $articles->count ();
	
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$articles = $this->articleTable->getLimitedArticlesOfMyDepartment($user_id,($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
	
		return new ViewModel ( array (
				'total_articles' => $totalResults,
				'articles' => $articles,
				'paginator' => $paginator
		) );
	}
	
	
	/**
	 * List all spare parts
	 */
	public function list1Action() {
		if (is_null ( $this->params ()->fromQuery ( 'perPage' ) )) {
			$resultsPerPage = 20;
		} else {
			$resultsPerPage = $this->params ()->fromQuery ( 'perPage' );
		}
		;
	
		if (is_null ( $this->params ()->fromQuery ( 'page' ) )) {
			$page = 1;
		} else {
			$page = $this->params ()->fromQuery ( 'page' );
		}
		;
	
		$spareparts = $this->sparePartTable->fetchAll ();
		$totalResults = $spareparts->count ();
	
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$spareparts = $this->sparePartTable->getLimitSpareParts ( ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		$this->layout('layout/no-layout');
		
	
		return new ViewModel ( array (
				'total_spareparts' => $totalResults,
				'spareparts' => $spareparts,
				'paginator' => $paginator
		) );
	}
	
	
	/*
	 * * 
	 */
	public function categoryAction() {
		$categories = $this->sparePartCategoryTable->getCategories1();
		
		return new ViewModel ( array (
				'assetCategories' =>$categories,
		));
		
		
	
	}
	
	/*
	 * *
	 */
	public function category1Action() {
	
		$categories = $this->sparePartCategoryTable->getCategories1();
		$this->layout('layout/no-layout');
		
		
		$viewModel = new ViewModel(array (
				'assetCategories' =>$categories,
		));		
		
		return $viewModel;
	}
	
	public function showCategoryAction() {
		if (is_null ( $this->params ()->fromQuery ( 'perPage' ) )) {
			$resultsPerPage = 20;
		} else {
			$resultsPerPage = $this->params ()->fromQuery ( 'perPage' );
		}
		
		if (is_null ( $this->params ()->fromQuery ( 'page' ) )) {
			$page = 1;
		} else {
			$page = $this->params ()->fromQuery ( 'page' );
		}
		
		$id = $this->params ()->fromQuery ( 'id' );
		
		$category = $this->sparePartCategoryTable->get ( $id );
		
		$spareparts = $this->sparePartCategoryMemberTable->getMembersByCatIdWithBalance( $id );
		$totalResults = $spareparts->count ();
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$spareparts = $this->sparePartCategoryMemberTable->getLimitMembersByCatIdWithBalance ($id, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		return new ViewModel ( array (
				'category' => $category,
				'total_spareparts' => $totalResults,
				'spareparts' => $spareparts,
				'paginator' => $paginator 
		) );
	}
	
	/**
	 * Show detail of a spare parts
	 */
	public function showAction() {
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$article = $this->articleTable->get ( $id );		
		$pictures = $this->articlePictureTable->getArticlePicturesById ( $id );
		
		return new ViewModel ( array (
				'article' => $article,
				'pictures' => $pictures,
			) );
	}
	
	
	public function picturesAction() {
		$id = ( int ) $this->params ()->fromQuery ( 'sparepart_id' );
		$sp = $this->sparePartTable->get ( $id );
		$pictures = $this->sparePartPictureTable->getSparepartPicturesById ( $id );

		return new ViewModel ( array (
				'sparepart' => $sp,
				'pictures' => $pictures,
			) );
	}
	
	
	
	
	
	

	
	
	public function showMovementAction() {
		$id = ( int ) $this->params ()->fromQuery ( 'sparepart_id' );
		$movements = $this->getSparepartMovementsTable ()->getMovementsOfSparepartByID ( $id );
		
		$sp = $this->sparePartTable->get ( $id );
		$pictures = $this->sparePartPictureTable->getSparepartPicturesById ( $id );
		
		return new ViewModel ( array (
				'sparepart' => $sp,
				'movements' => $movements,
				'pictures' => $pictures 
		) );
	}
	
	// SETTER AND GETTER
	
	public function getAuthService() {
		return $this->authService;
	}
	public function setAuthService($authService) {
		$this->authService = $authService;
		return $this;
	}
	public function getSmtpTransportService() {
		return $this->SmtpTransportService;
	}
	public function setSmtpTransportService($SmtpTransportService) {
		$this->SmtpTransportService = $SmtpTransportService;
		return $this;
	}
	public function getUserTable() {
		return $this->userTable;
	}
	public function setUserTable($userTable) {
		$this->userTable = $userTable;
		return $this;
	}
	public function getArticleTable() {
		return $this->articleTable;
	}
	public function setArticleTable(ArticleTable $articleTable) {
		$this->articleTable = $articleTable;
		return $this;
	}
	public function getArticleService() {
		return $this->articleService;
	}
	public function setArticleService(ArticleService $articleService) {
		$this->articleService = $articleService;
		return $this;
	}
	public function getArticlePictureTable() {
		return $this->articlePictureTable;
	}
	public function setArticlePictureTable(ArticlePictureTable $articlePictureTable) {
		$this->articlePictureTable = $articlePictureTable;
		return $this;
	}
	public function getArticleCategoryTable() {
		return $this->articleCategoryTable;
	}
	public function setArticleCategoryTable(ArticleCategoryTable $articleCategoryTable) {
		$this->articleCategoryTable = $articleCategoryTable;
		return $this;
	}
	public function getArticleCategoryMemberTable() {
		return $this->articleCategoryMemberTable;
	}
	public function setArticleCategoryMemberTable(ArticleCategoryMemberTable $articleCategoryMemberTable) {
		$this->articleCategoryMemberTable = $articleCategoryMemberTable;
		return $this;
	}
	public function getArticleMovementTable() {
		return $this->articleMovementTable;
	}
	public function setArticleMovementTable(ArticleMovementTable $articleMovementTable) {
		$this->articleMovementTable = $articleMovementTable;
		return $this;
	}
	public function getPurchaseRequestCartItemTable() {
		return $this->purchaseRequestCartItemTable;
	}
	public function setPurchaseRequestCartItemTable(PurchaseRequestCartItemTable $purchaseRequestCartItemTable) {
		$this->purchaseRequestCartItemTable = $purchaseRequestCartItemTable;
		return $this;
	}
	public function getArticleSearchService() {
		return $this->articleSearchService;
	}
	public function setArticleSearchService(ArticleSearchService $articleSearchService) {
		$this->articleSearchService = $articleSearchService;
		return $this;
	}
	
	
	
	
	
	
	
	
	
	

}
