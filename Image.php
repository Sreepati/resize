################# CakePHP\app\Model\Image.php
<?php

App::uses('AppModel', 'Model');
App::uses('Images', 'Plugin/Resize');

class Image extends AppModel {
	public $useTable = false; // This model does not use a database table
	
	public function resize( $filename, $height, $width){
		
		$site = Configure::read('App.siteUrl');// www.example.com
		$root = Configure::read('serverpath'); // /var/www/html/demo/app/webroot/images
		
		if (!is_file($root . $filename)) {
			return;
		}
		
		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		$old_image = $filename;
		mb_internal_encoding("UTF-8");
		$new_image = 'cache/' . mb_substr($filename, 0, mb_strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;
		
		if (!is_file($root. 'images/' . $new_image) || (filectime($root . $old_image) > filectime($root. 'images/' . $new_image))) {
			$path = '';

			$directories = explode('/', dirname(str_replace('../', '', $new_image)));
		

			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;

				if (!is_dir($root. 'images/' . $path)) {
					@mkdir($root. 'images/' . $path, 0777);
				}
			}

			list($width_orig, $height_orig) = getimagesize($root . $old_image);
		
			if ($width_orig != $width || $height_orig != $height) {
				$image = new Images($root . $old_image);
				$image->resize($width, $height);
				$image->save($root. 'images/' . $new_image);
			} else {
				copy($root . $old_image, $root. 'images/' . $new_image);
			}
		}

		return $site . 'images/' . $new_image;
	}
}
?>
