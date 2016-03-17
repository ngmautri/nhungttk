<?php

namespace Inventory\Listener;

use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;


class PictureUploadListener implements ListenerAggregateInterface {
	/**
	 *
	 * @var array
	 */
	protected $listeners = array ();

	public function attach(EventManagerInterface $events) {
		$this->listeners [] = $events->attach ( 'uploadPicture', array (
				$this,
				'createThumbnail' 
		), 200 );
	}
	
	public function detach(EventManagerInterface $events) {
		foreach ( $this->listeners as $index => $listener ) {
			if ($events->detach ( $listener )) {
				unset ( $this->listeners [$index] );
			}
		}
	}
	
	/**
	 * 
	 * @param EventInterface $e
	 */
	public function createThumbnail(EventInterface $e) {
		
		
		$name = $e->getParam ('picture_name');
		$pictures_dir = $e->getParam ('pictures_dir');
		

		if(preg_match('/[.](jpg)$/', $name)) {
			$im = imagecreatefromjpeg("$pictures_dir/$name");
		} else if (preg_match('/[.](gif)$/', $name)) {
			$im = imagecreatefromgif("$pictures_dir/$name");
		} else if (preg_match('/[.](png)$/', $name)) {
			$im = imagecreatefrompng("$pictures_dir/$name");
		}
			
		$ox = imagesx($im);
		$oy = imagesy($im);
		
		$final_width_of_image =450;
		
		$nx = $final_width_of_image;
		$ny = floor($oy * ($final_width_of_image / $ox));
			
		$nm = imagecreatetruecolor($nx, $ny);
		
		$name_thumbnail = 'thumbnail_450_'.$name ;
		
		
		imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
		
		if (preg_match ( '/[.](jpg)$/', $name_thumbnail )) {
			imagejpeg ( $nm, "$pictures_dir/$name_thumbnail" );
		} else if (preg_match ( '/[.](gif)$/', $name_thumbnail )) {
			imagegif( $nm, "$pictures_dir/$name_thumbnail" );
		} else if (preg_match('/[.](png)$/', $name_thumbnail)) {
			imagepng ( $nm, "$pictures_dir/$name_thumbnail" );
		}
		
		// 150
		$final_width_of_image =150;
		
		$nx = $final_width_of_image;
		$ny = floor($oy * ($final_width_of_image / $ox));
			
		$nm = imagecreatetruecolor($nx, $ny);
		
		$name_thumbnail = 'thumbnail_150_' . $name;
		
		imagecopyresized ( $nm, $im, 0, 0, 0, 0, $nx, $ny, $ox, $oy );
		
		if (preg_match ( '/[.](jpg)$/', $name_thumbnail )) {
			imagejpeg ( $nm, "$pictures_dir/$name_thumbnail" );
		} else if (preg_match ( '/[.](gif)$/', $name_thumbnail )) {
			imagegif( $nm, "$pictures_dir/$name_thumbnail" );
		} else if (preg_match('/[.](png)$/', $name_thumbnail)) {
			imagepng ( $nm, "$pictures_dir/$name_thumbnail" );
		}
		
		// 200
		$final_width_of_image =200;
		
		$nx = $final_width_of_image;
		$ny = floor($oy * ($final_width_of_image / $ox));
			
		$nm = imagecreatetruecolor($nx, $ny);
		
		$name_thumbnail = 'thumbnail_200_' . $name;
		
		imagecopyresized ( $nm, $im, 0, 0, 0, 0, $nx, $ny, $ox, $oy );
		
		if (preg_match ( '/[.](jpg)$/', $name_thumbnail )) {
			imagejpeg ( $nm, "$pictures_dir/$name_thumbnail" );
		} else if (preg_match ( '/[.](gif)$/', $name_thumbnail )) {
			imagegif( $nm, "$pictures_dir/$name_thumbnail" );
		} else if (preg_match('/[.](png)$/', $name_thumbnail)) {
			imagepng ( $nm, "$pictures_dir/$name_thumbnail" );
		}
		

	}
}