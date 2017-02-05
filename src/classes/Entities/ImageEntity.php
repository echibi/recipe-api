<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 05/02/17
 */

namespace App\Entities;


class ImageEntity extends Entity {

	/**
	 * @var int
	 */
	public $id;
	/**
	 * @var string
	 */
	public $filename;
	/**
	 * @var string
	 */
	public $alt;
	/**
	 * @var string
	 */
	public $mime_type;
	/**
	 * @var string
	 */
	public $ext;
	/**
	 * @var string
	 */
	public $path;
	/**
	 * @var string
	 */
	public $size;
	/**
	 * @var string Date
	 */
	public $created;
	/**
	 * @var string Date
	 */
	public $updated;

	/**
	 * Accept an array of data matching properties of this class
	 * and create the class
	 *
	 * @param array|object $data The data to use to create
	 */
	public function __construct( $data ) {
		if ( is_object( $data ) ) {
			$data = $this->objectToArray( $data );
		}
		// no id if we're creating
		if ( isset( $data['id'] ) ) {
			$this->id = $data['id'];
		}
		$this->filename  = $data['filename'];
		$this->alt       = $data['alt'];
		$this->mime_type = $data['mime_type'];
		$this->ext       = $data['ext'];
		$this->path      = $data['path'];
		$this->size      = $data['size'];
		$this->created   = $data['created'];
		$this->updated   = $data['updated'];
	}
}