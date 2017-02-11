<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 05/02/17
 */

namespace App\Upload;


use Intervention\Image\Constraint;
use Intervention\Image\ImageManager;
use Intervention\Image\Size;
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

		$filename = 'IMG_' . uniqid() . '.' . $this->getFileExtension( $file );
		$path     = $this->path . '/' . $filename;

		$this->createPath( $this->path );
		$file->moveTo( $path );

		$this->createThumbnails( $path );

		return $this->current_path . '/' . $filename;
	}


	public function createThumbnails( $file ) {

		$settings          = $this->container->get( 'settings' )['image_manager'];
		$thumbnailSettings = $settings['thumbnail_sizes'];

		/**
		 * @var ImageManager $manager
		 */
		$manager = $this->container->get( 'ImageManager' );

		// Strip file extension and use filename with affix when saving
		$ext      = '.' . end( explode( ".", $file ) );
		$saveName = str_replace( $ext, '', $file );

		/**
		 * Create all thumbnails as defined in settings.
		 */
		foreach ( $thumbnailSettings as $key => $setting ) {
			$image = $manager->make( $file );

			if ( true === $setting['square'] ) {
				$image->fit( $setting['w'], $setting['h'], function ( Constraint $constraint ) {
					$constraint->upsize();
				} );
			} else {
				$image->resize( $setting['w'], $setting['h'], function ( Constraint $constraint ) {
					$constraint->aspectRatio();
					$constraint->upsize();
				} );
			}

			$image->save( $saveName . '-' . $key . $ext, $settings['quality'] );
		}
	}
}