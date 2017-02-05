<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 05/02/17
 */

namespace App\Models;


use App\Entities\ImageEntity;
use App\Upload\ImageUpload;
use Slim\Http\UploadedFile;

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
		 * @var ImageUpload $imageUpload
		 */
		$imageUpload = $this->container->get( 'ImageUpload' );
		$file_path   = $imageUpload->upload( $image );

		$now   = date( 'Y-m-d H:i:s' );
		$query = $this->db->table( self::table );

		return $query->insert(
			array(
				'filename'    => $image->getClientFilename(),
				'alt'         => '', // Not implemented yet.
				'mime_type'   => $image->getClientMediaType(),
				'ext'         => $imageUpload->getFileExtension( $image ),
				'system_path' => $file_path,
				'size'        => $image->getSize(),
				'created'     => $now,
				'updated'     => $now
			)
		);
	}

	public function remove() {
	}

}