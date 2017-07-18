<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 05/02/17
 */

namespace App\Models;


use App\Entities\ImageEntity;
use App\Upload\Upload;
use Intervention\Image\Constraint;
use Intervention\Image\ImageManager;
use Slim\Http\Request;
use Slim\Http\UploadedFile;
use Slim\Http\Uri;
use Slim\Router;

/**
 * Class ImageModel
 * @package App\Models
 */
class ImageModel extends Model {
	const table = 'images';

	/**
	 * @param        $id
	 * @param string $field
	 *
	 * @return ImageEntity
	 */
	public function get( $id, $field = 'id' ) {
		$image = $this->db->table( self::table )->find( $id, $field );
		if ( null !== $image ) {
			$image = new ImageEntity( $image );
		}

		return $image;

	}

	public function create( UploadedFile $image ) {

		if ( $image->getError() !== UPLOAD_ERR_OK ) {
			$this->logger->addError( 'Image Upload failed.', array( 'image' => $image ) );

			return null;
		}

		/**
		 * @var Upload $imageUpload
		 */
		$imageUpload = $this->container->get( 'Upload' );
		$file_path   = $imageUpload->upload( $image );

		$this->createThumbnails( $file_path );

		$now   = date( 'Y-m-d H:i:s' );
		$query = $this->db->table( self::table );

		return $query->insert(
			array(
				'filename'  => $image->getClientFilename(),
				'alt'       => '', // Not implemented yet.
				'mime_type' => $image->getClientMediaType(),
				'ext'       => $imageUpload->getFileExtension( $image ),
				'path'      => $file_path,
				'size'      => $image->getSize(),
				'created'   => $now,
				'updated'   => $now
			)
		);
	}

	public function remove() {
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
				$image->fit(
					$setting['w'], $setting['h'], function ( Constraint $constraint ) {
					$constraint->upsize();
				}
				);
			} else {
				$image->resize(
					$setting['w'], $setting['h'], function ( Constraint $constraint ) {
					$constraint->aspectRatio();
					$constraint->upsize();
				}
				);
			}
			$affix = $this->getThumbnailAffix( $setting );
			$image->save( $saveName . '-' . $affix . $ext, $settings['quality'] );
		}
	}

	/**
	 * @param ImageEntity $image
	 * @param string      $size
	 *
	 * @return string
	 */
	public function getImageUrl( ImageEntity $image, $size = '' ) {

		$uploadFullPath = $this->container->get( 'settings' )['paths']['dir'];
		$sizes          = $this->container->get( 'settings' )['image_manager']['thumbnail_sizes'];

		$imageAffix = '';
		if ( isset( $sizes[$size] ) ) {
			$thumbnailSize = $this->getThumbnailAffix( $sizes[$size] );

			// Strip file extension and use filename with affix when saving
			$image->path = str_replace( '.' . $image->ext, '', $image->path );
			$imageAffix .= '-' . $thumbnailSize . '.' . $image->ext;
		}

		/**
		 * @var Request $request
		 */
		$request = $this->container->get( 'request' );
		/**
		 * @var Uri $uri
		 */
		$uri = $request->getUri();

		$uploadDir = substr( $uploadFullPath, strpos( $uploadFullPath, "public/" ) + 6 );
		$this->logger->addDebug(
			'getImageUrl',
			array(
				'base_url'    => $uri->getBaseUrl(),
				'upload_dir'  => $uploadDir,
				'image_path'  => $image->path,
				'image_affix' => $imageAffix,
				'full_url'    => $uri->getBaseUrl() . $uploadDir . $image->path . $imageAffix
			)
		);

		return $uri->getBaseUrl() . $uploadDir . $image->path . $imageAffix;
	}

	/**
	 * Returns an affix based on thumbnail size.
	 *
	 * @param $thumbnailSetting
	 *
	 * @return string
	 */
	private function getThumbnailAffix( $thumbnailSetting ) {
		$affix = '';
		if ( null !== $thumbnailSetting['w'] ) {
			$affix .= $thumbnailSetting['w'];
			if ( null !== $thumbnailSetting['h'] ) {
				$affix .= 'x' . $thumbnailSetting['h'];
			}
		} else {
			if ( null !== $thumbnailSetting['h'] ) {
				$affix .= $thumbnailSetting['h'];
			}
		}

		return $affix;
	}

}