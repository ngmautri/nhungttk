<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Inventory\Controller;

use PHPExcel;
use PHPExcel_IOFactory;

use Doctrine\ORM\EntityManager;
use Zend\I18n\Validator\Int;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\Validator\EmailAddress;
use Zend\Mail\Message;
use Zend\View\Model\ViewModel;
use Zend\Http\Headers;
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

//use Inventory\Model\MlaArticleDepartment;
use Inventory\Model\MlaArticleDepartmentTable;
use Procurement\Model\PurchaseRequestCartItemTable;
use Application\Model\DepartmentTable;

use Application\Entity\MlaArticleDepartment;


use User\Model\UserTable;

class ArticleController extends AbstractActionController {
	protected $SmtpTransportService;
	protected $authService;
	protected $articleService;
	protected $articleSearchService;
	protected $articleCategoryService;
	protected $userTable;
	protected $articleTable;
	protected $articleCategoryTable;
	protected $articleCategoryMemberTable;
	protected $articlePictureTable;
	protected $articleMovementTable;
	protected $purchaseRequestCartItemTable;
	protected $departmentTable;
	protected $doctrineEM;
	
	protected $mlaArticleDepartmentTable;
	
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
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		if ($request->isPost ()) {
			
			if ($request->isPost ()) {
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				
				$input = new Article ();
				$input->article_tag = $request->getPost ( 'article_tag' );
				$input->name = $request->getPost ( 'name' );
				$input->name_local = $request->getPost ( 'name_local' );
				
				$input->description = $request->getPost ( 'description' );
				
				$input->keywords = $request->getPost ( 'keywords' );
				$input->unit = $request->getPost ( 'unit' );
				
				$input->type = $request->getPost ( 'type' );
				$input->code = $request->getPost ( 'code' );
				
				$input->barcode = $request->getPost ( 'barcode' );
				
				$input->created_by = $user ['id'];
				
				$input->visibility = $request->getPost ( 'visibility' );
				$input->status = $request->getPost ( 'status' );
				$input->remarks = $request->getPost ( 'remarks' );
				
				$category_id = ( int ) $request->getPost ( 'category_id' );
				
				$errors = array ();
				
				if ($input->name == '') {
					$errors [] = 'Please give article name';
				}
				
				if (count ( $errors ) > 0) {
					return new ViewModel ( array (
							'errors' => $errors,
							'redirectUrl' => $redirectUrl,
							'category_id' => $category_id,
							'article' => $input 
					) );
				}
				
				$newId = $this->articleTable->add ( $input );
				$row = $this->articleTable->getArticleByID ( $newId );
				$this->articleSearchService->updateIndex ( $row );
				
				// update search index
				
				$root_dir = $this->articleService->getPicturesPath ();
				
				// $files = $request->getFiles ()->toArray ();
				
				$pictureUploadListener = $this->getServiceLocator ()->get ( 'Inventory\Listener\PictureUploadListener' );
				$this->getEventManager ()->attachAggregate ( $pictureUploadListener );
				
				$id = $newId;
				
				foreach ( $_FILES ["pictures"] ["error"] as $key => $error ) {
					if ($error == UPLOAD_ERR_OK) {
						$tmp_name = $_FILES ["pictures"] ["tmp_name"] [$key];
						
						$ext = strtolower ( pathinfo ( $_FILES ["pictures"] ["name"] [$key], PATHINFO_EXTENSION ) );
						
						if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'gif' || $ext == 'png') {
							
							$checksum = md5_file ( $tmp_name );
							
							if (! $this->articlePictureTable->isChecksumExits ( $id, $checksum )) {
								
								$name = md5 ( $id . $checksum . uniqid ( microtime () ) ) . '.' . $ext;
								$folder = $root_dir . DIRECTORY_SEPARATOR . $name [0] . $name [1] . DIRECTORY_SEPARATOR . $name [2] . $name [3] . DIRECTORY_SEPARATOR . $name [4] . $name [5];
								
								if (! is_dir ( $folder )) {
									mkdir ( $folder, 0777, true ); // important
								}
								
								$ftype = $_FILES ["pictures"] ["type"] [$key];
								move_uploaded_file ( $tmp_name, "$folder/$name" );
								
								// add pictures
								$pic = new ArticlePicture ();
								$pic->url = "$folder/$name";
								$pic->filetype = $ftype;
								$pic->article_id = $id;
								$pic->filename = "$name";
								$pic->folder = "$folder";
								$pic->checksum = $checksum;
								
								$this->articlePictureTable->add ( $pic );
								
								// trigger uploadPicture
								$this->getEventManager ()->trigger ( 'uploadPicture', __CLASS__, array (
										'picture_name' => $name,
										'pictures_dir' => $folder 
								) );
							}
						}
					}
				}
				
				// add category
				if ($category_id > 1) {
					$m = new ArticleCategoryMember ();
					$m->article_id = $newId;
					$m->article_cat_id = $category_id;
					$this->articleCategoryMemberTable->add ( $m );
				}
				
				$this->redirect ()->toUrl ( $redirectUrl );
			}
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$category_id = ( int ) $this->params ()->fromQuery ( 'category_id' );
		
		// add category
		if ($category_id > 1) {
			$category = $this->articleCategoryTable->get ( $category_id );
		} else {
			$category = null;
		}
		
		return new ViewModel ( array (
				'message' => 'Add new article',
				'category' => $category,
				'redirectUrl' => $redirectUrl,
				'errors' => null,
				'article' => null 
		) );
	}
	/**
	 * create New Article
	 */
	public function adminAddAction() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		if ($request->isPost ()) {
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			
			$input = new Article ();
			$input->article_tag = $request->getPost ( 'article_tag' );
			$input->name = $request->getPost ( 'name' );
			$input->name_local = $request->getPost ( 'name_local' );
			
			$input->description = $request->getPost ( 'description' );
			
			$input->keywords = $request->getPost ( 'keywords' );
			$input->unit = $request->getPost ( 'unit' );
			
			$input->type = $request->getPost ( 'type' );
			$input->code = $request->getPost ( 'code' );
			
			$input->barcode = $request->getPost ( 'barcode' );
			
			$input->created_by = $user ['id'];
			
			$input->visibility = $request->getPost ( 'visibility' );
			$input->status = $request->getPost ( 'status' );
			$input->remarks = $request->getPost ( 'remarks' );
			
			$category_id = ( int ) $request->getPost ( 'category_id' );
			$department_id = $request->getPost ( 'department_id' );
			
			$errors = array ();
			
			if ($input->name == '') {
				$errors [] = 'Please give article name';
			}
			
			if (count ( $errors ) > 0) {
				return new ViewModel ( array (
						'errors' => $errors,
						'redirectUrl' => $redirectUrl,
						'category_id' => $category_id,
						'article' => $input,
						'department_id' => $department_id 
				) );
			}
			
			$newId = $this->articleTable->add ( $input );
			$row = $this->articleTable->getArticleByID ( $newId );
			$this->articleSearchService->updateIndex ( $row );
			
			// update search index
			
			$root_dir = $this->articleService->getPicturesPath ();
			
			// $files = $request->getFiles ()->toArray ();
			
			$pictureUploadListener = $this->getServiceLocator ()->get ( 'Inventory\Listener\PictureUploadListener' );
			$this->getEventManager ()->attachAggregate ( $pictureUploadListener );
			
			$id = $newId;
			
			foreach ( $_FILES ["pictures"] ["error"] as $key => $error ) {
				if ($error == UPLOAD_ERR_OK) {
					$tmp_name = $_FILES ["pictures"] ["tmp_name"] [$key];
					
					$ext = strtolower ( pathinfo ( $_FILES ["pictures"] ["name"] [$key], PATHINFO_EXTENSION ) );
					
					if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'gif' || $ext == 'png') {
						
						$checksum = md5_file ( $tmp_name );
						
						if (! $this->articlePictureTable->isChecksumExits ( $id, $checksum )) {
							
							$name = md5 ( $id . $checksum . uniqid ( microtime () ) ) . '.' . $ext;
							$folder = $root_dir . DIRECTORY_SEPARATOR . $name [0] . $name [1] . DIRECTORY_SEPARATOR . $name [2] . $name [3] . DIRECTORY_SEPARATOR . $name [4] . $name [5];
							
							if (! is_dir ( $folder )) {
								mkdir ( $folder, 0777, true ); // important
							}
							
							$ftype = $_FILES ["pictures"] ["type"] [$key];
							move_uploaded_file ( $tmp_name, "$folder/$name" );
							
							// add pictures
							$pic = new ArticlePicture ();
							$pic->url = "$folder/$name";
							$pic->filetype = $ftype;
							$pic->article_id = $id;
							$pic->filename = "$name";
							$pic->folder = "$folder";
							$pic->checksum = $checksum;
							
							$this->articlePictureTable->add ( $pic );
							
							// trigger uploadPicture
							$this->getEventManager ()->trigger ( 'uploadPicture', __CLASS__, array (
									'picture_name' => $name,
									'pictures_dir' => $folder 
							) );
						}
					}
				}
			}
			
			// add category
			if ($category_id > 1) {
				$m = new ArticleCategoryMember ();
				$m->article_id = $newId;
				$m->article_cat_id = $category_id;
				$this->articleCategoryMemberTable->add ( $m );
			}
			
			// adding department
			// Get Department
			
			
			if ($department_id > 1) {
				
				
				$em = $this->doctrineEM;
				$d = new MlaArticleDepartment();
				
				$article = $this->doctrineEM->find( 'Application\Entity\MlaArticles', $id );
				$d->setArticle ( $article );
				$dep = $this->doctrineEM->find ('Application\Entity\MlaDepartments', $department_id );
				$d->setDepartment( $dep );
				
				$u = $this->doctrineEM->find ('Application\Entity\MlaUsers', $user ['id']);
				$d->setUpdatedBy( $u);
				$d->setUpdatedOn(new \DateTime());
								
				$em->persist ( $d );
				$em->flush ();
				//echo $d->getId();
				
	/* 			$input = new MlaArticleDepartment();
				$input->article_id = $id;
				$input->department_id = $department_id;
				$input->updated_by =  $user ['id'];
				$this->mlaArticleDepartmentTable->add($input); */			
			}
			
			//$this->redirect ()->toUrl ( $redirectUrl );
			
			
		}
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$category_id = ( int ) $this->params ()->fromQuery ( 'category_id' );
		
		// add category
		if ($category_id > 1) {
			$category = $this->articleCategoryTable->get ( $category_id );
		} else {
			$category = null;
		}
		$departments = $this->departmentTable->fetchAll ();
		
		return new ViewModel ( array (
				'message' => 'Add new article',
				'category' => $category,
				'redirectUrl' => $redirectUrl,
				'errors' => null,
				'article' => null,
				'departments' => $departments,
				'department_id' => null 
		
		) );
	}
	
	/**
	 * Show Movement of Article
	 */
	public function movementsAction() {
		$request = $this->getRequest ();
		
		$id = ( int ) $this->params ()->fromQuery ( 'article_id' );
		$movements = $this->articleMovementTable->getMovements ( $id );
		$article = $this->articleTable->getArticleByID ( $id );
		
		if ($request->isXmlHttpRequest ()) {
			$this->layout ( "layout/inventory/ajax" );
		}
		return new ViewModel ( array (
				'article' => $article,
				'movements' => $movements 
		) );
	}
	
	/**
	 * Receive Article /Item
	 */
	public function receiveAction() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		if ($request->isPost ()) {
			
			$request = $this->getRequest ();
			
			if ($request->isPost ()) {
				
				$input = new ArticleMovement ();
				$input->article_id = $request->getPost ( 'article_id' );
				$input->movement_date = $request->getPost ( 'movement_date' );
				
				$input->quantity = $request->getPost ( 'quantity' );
				
				$input->flow = 'IN';
				
				$input->reason = $request->getPost ( 'reason' );
				$input->requester = $request->getPost ( 'requester' );
				$input->comment = $request->getPost ( 'comment' );
				$input->created_on = $request->getPost ( 'created_on' );
				
				$instock = $request->getPost ( 'instock' );
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				
				$errors = array ();
				
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
				
				$id = ( int ) $request->getPost ( 'article_id' );
				$article = $this->articleTable->get ( $id );
				$pictures = $this->articlePictureTable->getArticlePicturesById ( $id );
				
				if (count ( $errors ) > 0) {
					return new ViewModel ( array (
							'article' => $article,
							'pictures' => $pictures,
							'instock' => $instock,
							'errors' => $errors,
							'redirectUrl' => $redirectUrl,
							'movement' => $input 
					) );
				} else {
					
					// Validated
					$newId = $this->articleMovementTable->add ( $input );
					
					$redirectUrl = $request->getPost ( 'redirectUrl' );
					$this->redirect ()->toUrl ( $redirectUrl );
				}
			}
		}
		
		$id = ( int ) $this->params ()->fromQuery ( 'article_id' );
		$article = $this->articleTable->getArticleByID ( $id );
		$pictures = $this->articlePictureTable->getArticlePicturesById ( $id );
		
		return new ViewModel ( array (
				'article' => $article,
				'pictures' => $pictures,
				'instock' => null,
				'errors' => null,
				'redirectUrl' => $redirectUrl,
				'movement' => null 
		) );
	}
	
	/**
	 * Issue Article
	 */
	public function issueAction() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		if ($request->isPost ()) {
			
			$input = new ArticleMovement ();
			$input->article_id = $request->getPost ( 'article_id' );
			$input->movement_date = $request->getPost ( 'movement_date' );
			$input->quantity = $request->getPost ( 'quantity' );
			$input->flow = 'OUT';
			$input->reason = $request->getPost ( 'reason' );
			$input->requester = $request->getPost ( 'requester' );
			$input->comment = $request->getPost ( 'comment' );
			$input->created_on = $request->getPost ( 'created_on' );
			
			$instock = $request->getPost ( 'instock' );
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			
			// validator.
			$validator = new Date ();
			
			$errors = array ();
			
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
				$article = $this->articleTable->getArticleByID ( $id );
				$pictures = $this->articlePictureTable->getArticlePicturesById ( $id );
				
				return new ViewModel ( array (
						'article' => $article,
						'pictures' => $pictures,
						'instock' => $instock,
						'errors' => $errors,
						'redirectUrl' => $redirectUrl,
						'movement' => $input 
				) );
			} else {
				
				// Validated
				$this->articleMovementTable->add ( $input );
				$this->redirect ()->toUrl ( $redirectUrl );
			}
		}
		
		$id = ( int ) $this->params ()->fromQuery ( 'article_id' );
		$article = $this->articleTable->getArticleByID ( $id );
		$pictures = $this->articlePictureTable->getArticlePicturesById ( $id );
		$instock = $article->article_balance;
		
		return new ViewModel ( array (
				'article' => $article,
				'pictures' => $pictures,
				'instock' => $instock,
				'errors' => null,
				'redirectUrl' => $redirectUrl,
				'movement' => null 
		) );
	}
	
	/**
	 * Article picture
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function picturesAction() {
		$id = ( int ) $this->params ()->fromQuery ( 'article_id' );
		$article = $this->articleTable->get ( $id );
		$pictures = $this->articlePictureTable->getArticlePicturesById ( $id );
		
		return new ViewModel ( array (
				'article' => $article,
				'pictures' => $pictures 
		) );
	}
	/**
	 * Edit Spare part
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function editAction() {
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			$id = $request->getPost ( 'id' );
			
			$input = new Article ();
			$input->article_tag = $request->getPost ( 'article_tag' );
			$input->name = $request->getPost ( 'name' );
			$input->name_local = $request->getPost ( 'name_local' );
			$input->description = $request->getPost ( 'description' );
			$input->keywords = $request->getPost ( 'keywords' );
			$input->unit = $request->getPost ( 'unit' );
			$input->type = $request->getPost ( 'type' );
			$input->code = $request->getPost ( 'code' );
			$input->barcode = $request->getPost ( 'barcode' );
			// $input->created_by = $user['id'];
			
			$input->visibility = $request->getPost ( 'visibility' );
			$input->status = $request->getPost ( 'status' );
			$input->remarks = $request->getPost ( 'remarks' );
			
			$errors = array ();
			
			if ($input->name == '') {
				$errors [] = 'Please give spare-part name';
			}
			
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			
			if (count ( $errors ) > 0) {
				// return current sp
				$article = $this->articleTable->get ( $input->id );
				
				return new ViewModel ( array (
						'errors' => $errors,
						'redirectUrl' => $redirectUrl,
						'article' => $article 
				) );
			}
			
			$this->articleTable->update ( $input, $id );
			$root_dir = $this->articleService->getPicturesPath ();
			
			// $files = $request->getFiles ()->toArray ();
			
			$pictureUploadListener = $this->getServiceLocator ()->get ( 'Inventory\Listener\PictureUploadListener' );
			$this->getEventManager ()->attachAggregate ( $pictureUploadListener );
			
			foreach ( $_FILES ["pictures"] ["error"] as $key => $error ) {
				if ($error == UPLOAD_ERR_OK) {
					$tmp_name = $_FILES ["pictures"] ["tmp_name"] [$key];
					
					$ext = strtolower ( pathinfo ( $_FILES ["pictures"] ["name"] [$key], PATHINFO_EXTENSION ) );
					
					if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'gif' || $ext == 'png') {
						
						$checksum = md5_file ( $tmp_name );
						
						if (! $this->articlePictureTable->isChecksumExits ( $id, $checksum )) {
							
							$name = md5 ( $id . $checksum . uniqid ( microtime () ) ) . '.' . $ext;
							$folder = $root_dir . DIRECTORY_SEPARATOR . $name [0] . $name [1] . DIRECTORY_SEPARATOR . $name [2] . $name [3] . DIRECTORY_SEPARATOR . $name [4] . $name [5];
							
							if (! is_dir ( $folder )) {
								mkdir ( $folder, 0777, true ); // important
							}
							
							$ftype = $_FILES ["pictures"] ["type"] [$key];
							move_uploaded_file ( $tmp_name, "$folder/$name" );
							
							// add pictures
							$pic = new ArticlePicture ();
							$pic->url = "$folder/$name";
							$pic->filetype = $ftype;
							$pic->article_id = $id;
							$pic->filename = "$name";
							$pic->folder = "$folder";
							$pic->checksum = $checksum;
							
							$this->articlePictureTable->add ( $pic );
							
							// trigger uploadPicture
							$this->getEventManager ()->trigger ( 'uploadPicture', __CLASS__, array (
									'picture_name' => $name,
									'pictures_dir' => $folder 
							) );
						}
					}
				}
			}
			
			$this->redirect ()->toUrl ( $redirectUrl );
			
			/*
			 * return new ViewModel ( array (
			 * 'article' => $article,
			 * 'redirectUrl' => $redirectUrl,
			 * 'errors' => null
			 * ) );
			 */
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$article = $this->articleTable->get ( $id );
		
		return new ViewModel ( array (
				'article' => $article,
				'redirectUrl' => $redirectUrl,
				'errors' => null 
		) );
	}
	
	/**
	 * Upload Article Picture
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function uploadPictureAction() {
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			$id = $request->getPost ( 'id' );
			$root_dir = $this->articleService->getPicturesPath ();
			
			// $files = $request->getFiles ()->toArray ();
			
			$pictureUploadListener = $this->getServiceLocator ()->get ( 'Inventory\Listener\PictureUploadListener' );
			$this->getEventManager ()->attachAggregate ( $pictureUploadListener );
			
			foreach ( $_FILES ["pictures"] ["error"] as $key => $error ) {
				if ($error == UPLOAD_ERR_OK) {
					$tmp_name = $_FILES ["pictures"] ["tmp_name"] [$key];
					
					$ext = strtolower ( pathinfo ( $_FILES ["pictures"] ["name"] [$key], PATHINFO_EXTENSION ) );
					
					if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'gif' || $ext == 'png') {
						
						$checksum = md5_file ( $tmp_name );
						
						if (! $this->articlePictureTable->isChecksumExits ( $id, $checksum )) {
							
							$name = md5 ( $id . $checksum . uniqid ( microtime () ) ) . '.' . $ext;
							$folder = $root_dir . DIRECTORY_SEPARATOR . $name [0] . $name [1] . DIRECTORY_SEPARATOR . $name [2] . $name [3] . DIRECTORY_SEPARATOR . $name [4] . $name [5];
							
							if (! is_dir ( $folder )) {
								mkdir ( $folder, 0777, true ); // important
							}
							
							$ftype = $_FILES ["pictures"] ["type"] [$key];
							move_uploaded_file ( $tmp_name, "$folder/$name" );
							
							// add pictures
							$pic = new ArticlePicture ();
							$pic->url = "$folder/$name";
							$pic->filetype = $ftype;
							$pic->article_id = $id;
							$pic->filename = "$name";
							$pic->folder = "$folder";
							$pic->checksum = $checksum;
							
							$this->articlePictureTable->add ( $pic );
							
							// trigger uploadPicture
							$this->getEventManager ()->trigger ( 'uploadPicture', __CLASS__, array (
									'picture_name' => $name,
									'pictures_dir' => $folder 
							) );
						}
					}
				}
			}
			
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			$this->redirect ()->toUrl ( $redirectUrl );
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$article = $this->articleTable->get ( $id );
		
		return new ViewModel ( array (
				'article' => $article,
				'redirectUrl' => $redirectUrl,
				'errors' => null 
		) );
	}
	
	/**
	 *
	 * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
	 */
	public function uploadPicture1Action() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		if ($request->isPost ()) {
			
			$pictures = $_POST ['pictures'];
			$id = $_POST ['article_id'];
			
			foreach ( $pictures as $p ) {
				
				$filetype = $p [0];
				if (preg_match ( '/(jpg|jpeg)$/', $filetype )) {
					$ext = 'jpg';
				} else if (preg_match ( '/(gif)$/', $filetype )) {
					$ext = 'gif';
				} else if (preg_match ( '/(png)$/', $filetype )) {
					$ext = 'png';
				}
				
				$tmp_name = md5 ( $id . uniqid ( microtime () ) ) . '.' . $ext;
				
				// remove "data:image/png;base64,"
				$uri = substr ( $p [1], strpos ( $p [1], "," ) + 1 );
				
				// save to file
				file_put_contents ( $tmp_name, base64_decode ( $uri ) );
				
				$checksum = md5_file ( $tmp_name );
				
				$root_dir = $this->articleService->getPicturesPath ();
				
				$pictureUploadListener = $this->getServiceLocator ()->get ( 'Inventory\Listener\PictureUploadListener' );
				$this->getEventManager ()->attachAggregate ( $pictureUploadListener );
				
				if (! $this->articlePictureTable->isChecksumExits ( $id, $checksum )) {
					$name = md5 ( $id . $checksum . uniqid ( microtime () ) ) . '.' . $ext;
					
					$folder = $root_dir . DIRECTORY_SEPARATOR . $name [0] . $name [1] . DIRECTORY_SEPARATOR . $name [2] . $name [3] . DIRECTORY_SEPARATOR . $name [4] . $name [5];
					
					if (! is_dir ( $folder )) {
						mkdir ( $folder, 0777, true ); // important
					}
					
					rename ( $tmp_name, "$folder/$name" );
					
					// add pictures
					$pic = new ArticlePicture ();
					$pic->url = "$folder/$name";
					$pic->filetype = $filetype;
					$pic->article_id = $id;
					$pic->filename = "$name";
					$pic->folder = "$folder";
					$pic->checksum = $checksum;
					
					$this->articlePictureTable->add ( $pic );
					
					// trigger uploadPicture
					$this->getEventManager ()->trigger ( 'uploadPicture', __CLASS__, array (
							'picture_name' => $name,
							'pictures_dir' => $folder 
					) );
				}
			}
			
			$data = array ();
			$data ['id'] = $id;
			// $data['filetype'] = $filetype;
			
			$response = $this->getResponse ();
			$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
			$response->setContent ( json_encode ( $data ) );
			return $response;
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$article = $this->articleTable->get ( $id );
		
		return new ViewModel ( array (
				'article' => $article,
				'redirectUrl' => $redirectUrl,
				'errors' => null 
		) );
	}
	
	/**
	 * List all articles Ò usser
	 */
	public function listAction() {
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$user_id = $user ['id'];
		
		$output = $this->params ()->fromQuery ( 'output' );
		$sort_by = $this->params ()->fromQuery ( 'sort_by' );
		$item_status = $this->params ()->fromQuery ( 'item_status' );
		$item_type = $this->params ()->fromQuery ( 'item_type' );
		
		if ($item_status == null) :
			$item_status = "Activated";
		
		endif;
		
		if ($sort_by == null) :
			$sort_by = "item_name";
		
		endif;
		
		if ($output === 'csv') {
			
			$fh = fopen ( 'php://memory', 'w' );
			// $myfile = fopen('ouptut.csv', 'a+');
			
			$h = array ();
			$h [] = "Item#";
			$h [] = "Item SKU";
			$h [] = "Item Name";
			$h [] = "Item Name (LA)";
			$h [] = "Description";
			$h [] = "Keywords";
			$h [] = "Type";
			$h [] = "Unit";
			$h [] = "Item Code";
			$h [] = "Item BarCode";
			$h [] = "Current Balace";
			$h [] = "Department Name";
			$h [] = "Status";
			
			$h [] = "Remarks	";
			
			$delimiter = ";";
			
			fputcsv ( $fh, $h, $delimiter, '"' );
			// fputs($fh, implode($h, ',')."\n");
			
			$articles = $this->articleTable->getArticles_V01 ( $user_id, null, null, 'item_name', 0, 0 );
			
			foreach ( $articles as $m ) {
				$l = array ();
				$l [] = ( string ) $m->id;
				$l [] = ( string ) $m->article_tag;
				
				$name = ( string ) $m->name;
				
				$name === '' ? $name = "-" : $name;
				
				$name = str_replace ( ',', '', $name );
				$name = str_replace ( ';', '', $name );
				
				$l [] = $name;
				$l [] = ( string ) $m->name_local;
				
				$l [] = ( string ) $m->description;
				$l [] = ( string ) $m->keywords;
				$l [] = ( string ) $m->type;
				$l [] = ( string ) $m->unit;
				$l [] = ( string ) "'" . $m->code;
				$l [] = ( string ) $m->barcode;
				$l [] = ( string ) $m->article_balance;
				$l [] = ( string ) $m->department_name;
				$l [] = ( string ) $m->status;
				$l [] = ( string ) $m->remarks;
				
				fputcsv ( $fh, $l, $delimiter, '"' );
				// fputs($fh, implode($l, ',')."\n");
			}
			
			$fileName = 'Items-' . date ( "m-d-Y" ) . '-' . date ( "h:i:sa" ) . '.csv';
			fseek ( $fh, 0 );
			$output = stream_get_contents ( $fh );
			// file_put_contents($fileName, $output);
			
			$response = $this->getResponse ();
			$headers = new Headers ();
			
			$headers->addHeaderLine ( 'Content-Type: text/csv' );
			// $headers->addHeaderLine ( 'Content-Type: application/vnd.ms-excel; charset=UTF-8' );
			
			$headers->addHeaderLine ( 'Content-Disposition: attachment; filename="' . $fileName . '"' );
			$headers->addHeaderLine ( 'Content-Description: File Transfer' );
			$headers->addHeaderLine ( 'Content-Transfer-Encoding: binary' );
			$headers->addHeaderLine ( 'Content-Encoding: UTF-8' );
			
			// $response->setHeaders(Headers::fromString("Content-Type: application/octet-stream\r\nContent-Length: 9\r\nContent-Disposition: attachment; filename=\"blamoo.txt\""));
			$response->setHeaders ( $headers );
			// $output = fread($fh, 8192);
			
			$response->setContent ( $output );
			
			fclose ( $fh );
			// unlink($fileName);
			return $response;
		}
		
		if ($output === 'order_template') {
			
			$fh = fopen ( 'php://memory', 'w' );
			// $myfile = fopen('ouptut.csv', 'a+');
			
			$h = array ();
			$h [] = "Item#";
			$h [] = "Item Name";
			$h [] = "Item Code";
			$h [] = "Item Unit";
			$h [] = "Order Quantity";
			$h [] = "EDT (Delivery Date)";
			$h [] = "Remarks";
			
			$delimiter = ";";
			
			fputcsv ( $fh, $h, $delimiter, '"' );
			// fputs($fh, implode($h, ',')."\n");
			
			$articles = $this->articleTable->getArticles_V01 ( $user_id, null, 'Activated', 'item_name', 0, 0 );
			
			foreach ( $articles as $m ) {
				$l = array ();
				$l [] = ( string ) $m->id;
				
				$name = ( string ) $m->name;
				
				$name === '' ? $name = "-" : $name;
				
				$name = str_replace ( ',', '', $name );
				$name = str_replace ( ';', '', $name );
				
				$l [] = $name;
				$l [] = ( string ) "'" . $m->code;
				$l [] = ( string ) $m->unit;
				$l [] = ( string ) '';
				$l [] = ( string ) '';
				$l [] = ( string ) '';
				
				fputcsv ( $fh, $l, $delimiter, '"' );
				// fputs($fh, implode($l, ',')."\n");
			}
			
			$fileName = 'Order_Template.csv';
			fseek ( $fh, 0 );
			$output = stream_get_contents ( $fh );
			// file_put_contents($fileName, $output);
			
			$response = $this->getResponse ();
			$headers = new Headers ();
			
			$headers->addHeaderLine ( 'Content-Type: text/csv' );
			// $headers->addHeaderLine ( 'Content-Type: application/vnd.ms-excel; charset=UTF-8' );
			
			$headers->addHeaderLine ( 'Content-Disposition: attachment; filename="' . $fileName . '"' );
			$headers->addHeaderLine ( 'Content-Description: File Transfer' );
			$headers->addHeaderLine ( 'Content-Transfer-Encoding: binary' );
			$headers->addHeaderLine ( 'Content-Encoding: UTF-8' );
			
			// $response->setHeaders(Headers::fromString("Content-Type: application/octet-stream\r\nContent-Length: 9\r\nContent-Disposition: attachment; filename=\"blamoo.txt\""));
			$response->setHeaders ( $headers );
			// $output = fread($fh, 8192);
			
			$response->setContent ( $output );
			
			fclose ( $fh );
			// unlink($fileName);
			return $response;
		}
		
		if (is_null ( $this->params ()->fromQuery ( 'perPage' ) )) {
			$resultsPerPage = 15;
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
		
		$articles = $this->articleTable->getArticles_V01 ( $user_id, $item_type, $item_status, $sort_by, 0, 0 );
		$totalResults = $articles->count ();
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$articles = $this->articleTable->getArticles_V01 ( $user_id, $item_type, $item_status, $sort_by, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		return new ViewModel ( array (
				'total_articles' => $totalResults,
				'articles' => $articles,
				'paginator' => $paginator,
				'sort_by' => $sort_by,
				'item_status' => $item_status,
				'per_pape' => $resultsPerPage,
				'item_type' => $item_type 
		) );
	}
	
	/**
	 * List all articles ADMIN
	 */
	public function allAction() {
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		$departments = $this->departmentTable->fetchAll ();
		
		$output = $this->params ()->fromQuery ( 'output' );
		$sort_by = $this->params ()->fromQuery ( 'sort_by' );
		$item_status = $this->params ()->fromQuery ( 'item_status' );
		$item_type = $this->params ()->fromQuery ( 'item_type' );
		$department_id = $this->params ()->fromQuery ( 'department_id' );
		
		if ($item_status == null) :
			$item_status = "Activated";
		 elseif ($item_status == "all") :
			$item_status = null;
		endif;
		
		if ($sort_by == null) :
			$sort_by = "item_name";
		
		endif;
		
		if ($output === 'csv') {
			
			$fh = fopen ( 'php://memory', 'w' );
			// $myfile = fopen('ouptut.csv', 'a+');
			
			$h = array ();
			$h [] = "Item#";
			$h [] = "Item SKU";
			$h [] = "Item Name";
			$h [] = "Item Name (LA)";
			$h [] = "Description";
			$h [] = "Keywords";
			$h [] = "Type";
			$h [] = "Unit";
			$h [] = "Item Code";
			$h [] = "Item BarCode";
			$h [] = "Current Balace";
			$h [] = "Department Name";
			$h [] = "Status";
			
			$h [] = "Remarks	";
			
			$delimiter = ";";
			
			fputcsv ( $fh, $h, $delimiter, '"' );
			// fputs($fh, implode($h, ',')."\n");
			
			$articles = $this->articleTable->getArticles_V02 ( 0, null, null, 'item_name', 0, 0 );
			
			foreach ( $articles as $m ) {
				$l = array ();
				$l [] = ( string ) $m->id;
				$l [] = ( string ) $m->article_tag;
				
				$name = ( string ) $m->name;
				
				$name === '' ? $name = "-" : $name;
				
				$name = str_replace ( ',', '', $name );
				$name = str_replace ( ';', '', $name );
				
				$l [] = $name;
				$l [] = ( string ) $m->name_local;
				
				$l [] = ( string ) $m->description;
				$l [] = ( string ) $m->keywords;
				$l [] = ( string ) $m->type;
				$l [] = ( string ) $m->unit;
				$l [] = ( string ) "'" . $m->code;
				$l [] = ( string ) $m->barcode;
				$l [] = ( string ) $m->article_balance;
				$l [] = ( string ) $m->department_name;
				$l [] = ( string ) $m->status;
				$l [] = ( string ) $m->remarks;
				
				fputcsv ( $fh, $l, $delimiter, '"' );
				// fputs($fh, implode($l, ',')."\n");
			}
			
			$fileName = 'Items-' . date ( "m-d-Y" ) . '-' . date ( "h:i:sa" ) . '.csv';
			fseek ( $fh, 0 );
			$output = stream_get_contents ( $fh );
			// file_put_contents($fileName, $output);
			
			$response = $this->getResponse ();
			$headers = new Headers ();
			
			$headers->addHeaderLine ( 'Content-Type: text/csv' );
			// $headers->addHeaderLine ( 'Content-Type: application/vnd.ms-excel; charset=UTF-8' );
			
			$headers->addHeaderLine ( 'Content-Disposition: attachment; filename="' . $fileName . '"' );
			$headers->addHeaderLine ( 'Content-Description: File Transfer' );
			$headers->addHeaderLine ( 'Content-Transfer-Encoding: binary' );
			$headers->addHeaderLine ( 'Content-Encoding: UTF-8' );
			
			// $response->setHeaders(Headers::fromString("Content-Type: application/octet-stream\r\nContent-Length: 9\r\nContent-Disposition: attachment; filename=\"blamoo.txt\""));
			$response->setHeaders ( $headers );
			// $output = fread($fh, 8192);
			
			$response->setContent ( $output );
			
			fclose ( $fh );
			// unlink($fileName);
			return $response;
		}
		
		if ($output === 'xlxs') {
			// Create new PHPExcel object
			$objPHPExcel = new PHPExcel ();
			// Set document properties
			$objPHPExcel->getProperties ()->setCreator ( "nmt@mascot.dl" )->setLastModifiedBy ( "Nguyen Mau Tri" )->setTitle ( "Office 2007 XLSX Test Document" )->setSubject ( "Office 2007 XLSX Test Document" )->setDescription ( "Test document for Office 2007 XLSX, generated using PHP classes." )->setKeywords ( "office 2007 openxml php" )->setCategory ( "Test result file" );
			// Add some data
			$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValue ( 'A1', 'Hello' )->setCellValue ( 'B2', 'world!' )->setCellValue ( 'C1', 'Hello' )->setCellValue ( 'D2', 'world!' );
			// Miscellaneous glyphs, UTF-8
			$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValue ( 'A4', 'Miscellaneous glyphs' )->setCellValue ( 'A5', 'éàèùâêîôûëïüÿäöüç' );
			// Rename worksheet
			$objPHPExcel->getActiveSheet ()->setTitle ( 'Items' );
			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex ( 0 );
			// Redirect output to a client’s web browser (Excel2007)
			header ( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
			header ( 'Content-Disposition: attachment;filename="01simple.xlsx"' );
			header ( 'Cache-Control: max-age=0' );
			// If you're serving to IE 9, then the following may be needed
			header ( 'Cache-Control: max-age=1' );
			// If you're serving to IE over SSL, then the following may be needed
			header ( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' ); // Date in the past
			header ( 'Last-Modified: ' . gmdate ( 'D, d M Y H:i:s' ) . ' GMT' ); // always modified
			header ( 'Cache-Control: cache, must-revalidate' ); // HTTP/1.1
			header ( 'Pragma: public' ); // HTTP/1.0
			$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel2007' );
			$objWriter->save ( 'php://output' );
			exit ();
		}
		
		if ($output === 'order_template') {
			
			$fh = fopen ( 'php://memory', 'w' );
			// $myfile = fopen('ouptut.csv', 'a+');
			
			$h = array ();
			$h [] = "Item#";
			$h [] = "Item Name";
			$h [] = "Item Code";
			$h [] = "Item Unit";
			$h [] = "Order Quantity";
			$h [] = "EDT (Delivery Date)";
			$h [] = "Remarks";
			
			$delimiter = ";";
			
			fputcsv ( $fh, $h, $delimiter, '"' );
			// fputs($fh, implode($h, ',')."\n");
			
			$articles = $this->articleTable->getArticles_V02 ( $department_id, null, 'Activated', 'item_name', 0, 0 );
			
			foreach ( $articles as $m ) {
				$l = array ();
				$l [] = ( string ) $m->id;
				
				$name = ( string ) $m->name;
				
				$name === '' ? $name = "-" : $name;
				
				$name = str_replace ( ',', '', $name );
				$name = str_replace ( ';', '', $name );
				
				$l [] = $name;
				$l [] = ( string ) "'" . $m->code;
				$l [] = ( string ) $m->unit;
				$l [] = ( string ) '';
				$l [] = ( string ) '';
				$l [] = ( string ) '';
				
				fputcsv ( $fh, $l, $delimiter, '"' );
				// fputs($fh, implode($l, ',')."\n");
			}
			
			$fileName = 'Order_Template.csv';
			fseek ( $fh, 0 );
			$output = stream_get_contents ( $fh );
			// file_put_contents($fileName, $output);
			
			$response = $this->getResponse ();
			$headers = new Headers ();
			
			$headers->addHeaderLine ( 'Content-Type: text/csv' );
			// $headers->addHeaderLine ( 'Content-Type: application/vnd.ms-excel; charset=UTF-8' );
			
			$headers->addHeaderLine ( 'Content-Disposition: attachment; filename="' . $fileName . '"' );
			$headers->addHeaderLine ( 'Content-Description: File Transfer' );
			$headers->addHeaderLine ( 'Content-Transfer-Encoding: binary' );
			$headers->addHeaderLine ( 'Content-Encoding: UTF-8' );
			
			// $response->setHeaders(Headers::fromString("Content-Type: application/octet-stream\r\nContent-Length: 9\r\nContent-Disposition: attachment; filename=\"blamoo.txt\""));
			$response->setHeaders ( $headers );
			// $output = fread($fh, 8192);
			
			$response->setContent ( $output );
			
			fclose ( $fh );
			// unlink($fileName);
			return $response;
		}
		
		if (is_null ( $this->params ()->fromQuery ( 'perPage' ) )) {
			$resultsPerPage = 15;
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
		
		$articles = $this->articleTable->getArticles_V02 ( $department_id, $item_type, $item_status, $sort_by, 0, 0 );
		$totalResults = $articles->count ();
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$articles = $this->articleTable->getArticles_V02 ( $department_id, $item_type, $item_status, $sort_by, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		return new ViewModel ( array (
				'total_articles' => $totalResults,
				'articles' => $articles,
				'paginator' => $paginator,
				'sort_by' => $sort_by,
				'item_status' => $item_status,
				'per_pape' => $resultsPerPage,
				'item_type' => $item_type,
				'departments' => $departments,
				'department_id' => $department_id 
		) );
	}
	
	/*
	 *
	 */
	public function myCategoryAction() {
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$user_id = $user ['id'];
		
		// var_dump($this->userTable->getUserDepartment($user_id));
		$root = $this->userTable->getUserDepartment ( $user_id )->article_root_category;
		// $root=43;
		
		$list = $this->articleCategoryService;
		$list = $list->initCategory ();
		$list = $list->updateCategory ( $root, 0 );
		$list = $list->generateJSTreeWithTotalMember ( $root );
		
		$this->layout ( "layout/fluid" );
		return new ViewModel ( array (
				'jsTree' => $list->getJSTree () 
		) );
	}
	public function categoryAction() {
		$list = $this->articleCategoryService;
		$list = $list->initCategory ();
		$list = $list->updateCategory ( 2, 0 );
		$list = $list->generateJSTreeWithTotalMember ( 2 );
		$this->layout ( "layout/fluid" );
		
		return new ViewModel ( array (
				'jsTree' => $list->getJSTree () 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function addCategoryAction() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		if ($request->isPost ()) {
			if ($request->isPost ()) {
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				$input = new ArticleCategory ();
				$input->name = $request->getPost ( 'name' );
				$input->parent_id = $request->getPost ( 'parent_id' );
				$input->created_by = $user ["id"];
				$errors = array ();
				
				if ($input->name == '') {
					$errors [] = 'Please give category name';
				}
				
				if (count ( $errors ) > 0) {
					return new ViewModel ( array (
							'errors' => $errors,
							'category' => $input 
					) );
				}
				
				$cat_id = $request->getPost ( 'cat_id' );
				
				// UPDATE
				if ($this->articleCategoryTable->isExits ( $cat_id ) === true) {
					$this->articleCategoryTable->update ( $input, $cat_id );
				} else {
					
					// role name must unique
					$new_role_id = $this->articleCategoryTable->add ( $input );
					if ($input->parent_id === '') :
						$input->parent_id = null;
					
					
					
					
					
					
					endif;
					
						// get path of parent and update new role
					if ($input->parent_id !== null) :
						$path = $this->articleCategoryTable->get ( $input->parent_id )->path;
						$path = $path . $new_role_id . '/';
						$input->path = $path;
					else :
						$input->path = $new_role_id . '/';
					endif;
					
					$a = explode ( '/', $input->path );
					$input->path_depth = count ( $a ) - 1;
					$new_role_id = $this->articleCategoryTable->update ( $input, $new_role_id );
				}
				$this->redirect ()->toUrl ( $redirectUrl );
			}
		}
	}
	
	/**
	 * JSON Status; Messages.
	 *
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function deleteCategoryAction() {
		$cat_id = $this->params ()->fromQuery ( 'cat_id' );
		$cat = $this->articleCategoryTable->getArticleCategoy ( $cat_id );
		$response = $this->getResponse ();
		
		if ($cat === null) {
			$c = array (
					'status' => '0',
					'messages' => array (
							"Category not found" 
					) 
			);
			$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
			$response->setContent ( json_encode ( $c ) );
			return $response;
		}
		
		$totalMembers = $cat->totalMembers;
		$totalChildren = $cat->totalChildren;
		
		$errors = array ();
		if ($totalMembers > 0) {
			$errors [] = "This category has member";
		}
		
		if ($totalChildren > 0) {
			$errors [] = "This category has sub-category";
		}
		
		if (count ( $errors ) > 0) {
			$c = array (
					'status' => '0',
					'messages' => $errors 
			);
			$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
			$response->setContent ( json_encode ( $c ) );
			return $response;
		} else {
			
			// Now ok to delete.
			$this->articleCategoryTable->delete ( $cat_id );
			
			$c = array (
					'status' => '1',
					'messages' => array (
							"deleted" 
					) 
			);
			$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
			$response->setContent ( json_encode ( $c ) );
			return $response;
		}
	}
	public function addCategoryMemberAction() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		if ($request->isPost ()) {
			
			$role_id = ( int ) $request->getPost ( 'id' );
			$user_id_list = $request->getPost ( 'users' );
			
			if (count ( $user_id_list ) > 0) {
				
				foreach ( $user_id_list as $user_id ) {
					$member = new AclUserRole ();
					$member->role_id = $role_id;
					$member->user_id = $user_id;
					$member->updated_by = $user ['id'];
					
					if ($this->aclUserRoleTable->isMember ( $user_id, $role_id ) == false) {
						$this->aclUserRoleTable->add ( $member );
					}
				}
				
				/*
				 * return new ViewModel ( array (
				 * 'sparepart' => null,
				 * 'categories' => $categories,
				 *
				 * ) );
				 */
				
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				$this->redirect ()->toUrl ( $redirectUrl );
			}
		}
		
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$cat = $this->aclRoleTable->getRole ( $id );
		
		$articles = $this->articleCategoryMemberTable->getNoneMembersOfCategory ( $id );
		
		return new ViewModel ( array (
				'cat' => $cat,
				'articles' => $articles,
				'redirectUrl' => $redirectUrl 
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
		
		$articles = $this->articleTable->getAllWithLastDO ();
		$totalResults = $articles->count ();
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$articles = $this->articleTable->getAllWithLastDOWithLimit ( ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
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
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$user_id = $user ['id'];
		
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
		
		$articles = $this->articleTable->getArticlesOf ( $user_id );
		$totalResults = $articles->count ();
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$articles = $this->articleTable->getLimittedArticlesOf ( $user_id, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
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
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$user_id = $user ['id'];
		
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
		
		$articles = $this->articleTable->getArticlesOfMyDepartment ( $user_id );
		$totalResults = $articles->count ();
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$articles = $this->articleTable->getLimitedArticlesOfMyDepartment ( $user_id, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
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
		
		$this->layout ( 'layout/no-layout' );
		
		return new ViewModel ( array (
				'total_spareparts' => $totalResults,
				'spareparts' => $spareparts,
				'paginator' => $paginator 
		) );
	}
	
	/*
	 * *
	 */
	public function category1Action() {
		$categories = $this->sparePartCategoryTable->getCategories1 ();
		$this->layout ( 'layout/no-layout' );
		
		$viewModel = new ViewModel ( array (
				'assetCategories' => $categories 
		) );
		
		return $viewModel;
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function showCategoryAction() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$user_id = $user ['id'];
		
		$cat_id = $this->params ()->fromQuery ( 'cat_id' );
		if ($cat_id > 0) {
			$articles = $this->articleTable->getArticlesOfCategory ( $cat_id, 0, 0 );
		} else {
			$articles = $this->articleTable->getUncategorizedArticlesOfUser ( $user_id, 0, 0 );
		}
		$total_articles = count ( $articles );
		$paginator = null;
		
		if ($request->isXmlHttpRequest ()) {
			$this->layout ( "layout/inventory/ajax" );
		}
		
		return new ViewModel ( array (
				'articles' => $articles,
				'total_articles' => $total_articles,
				'paginator' => $paginator 
		
		) );
	}
	
	/**
	 * Show detail of a spare parts
	 */
	public function showAction() {
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$article = $this->articleTable->get ( $id );
		$pictures = $this->articlePictureTable->getArticlePicturesById ( $id );
		
		return new ViewModel ( array (
				'article' => $article,
				'pictures' => $pictures,
				'back' => $redirectUrl 
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
	public function setUserTable(UserTable $userTable) {
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
	public function getArticleCategoryService() {
		return $this->articleCategoryService;
	}
	public function setArticleCategoryService(\User\Service\ArticleCategory $articleCategoryService) {
		$this->articleCategoryService = $articleCategoryService;
		return $this;
	}
	public function getDepartmentTable() {
		return $this->departmentTable;
	}
	public function setDepartmentTable(DepartmentTable $departmentTable) {
		$this->departmentTable = $departmentTable;
		return $this;
	}
	public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
	public function getMlaArticleDepartmentTable() {
		return $this->mlaArticleDepartmentTable;
	}
	public function setMlaArticleDepartmentTable(MlaArticleDepartmentTable $mlaArticleDepartmentTable) {
		$this->mlaArticleDepartmentTable = $mlaArticleDepartmentTable;
		return $this;
	}
	
}
