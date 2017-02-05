<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 05/02/17
 */

namespace App\Upload;


use Slim\Http\UploadedFile;

/**
 * Class ImageUpload
 * @package App\Upload
 */
class ImageUpload extends Upload {
	/**
	 * Renames and moves file to correct location
	 *
	 * @param UploadedFile $file
	 *
	 * @return string Full path to file
	 */
	public function upload( UploadedFile $file ) {
		// TODO:: Generate thumbnails somewhere.

		$filename = uniqid( 'img_', true ) . '.' . $this->getFileExtension( $file );
		$path     = $this->path . '/' . $filename;

		$this->createPath( $this->path );
		$file->moveTo( $path );

		return $this->current_path . '/' . $filename;
	}
}