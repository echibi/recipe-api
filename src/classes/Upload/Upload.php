<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 05/02/17
 */

namespace App\Upload;


use App\Helpers\Utilities;
use Interop\Container\ContainerInterface;
use Monolog\Logger;
use Slim\Http\UploadedFile;

/**
 * Class Upload
 * @package App\Upload
 */
class Upload {
	/**
	 * @var ContainerInterface
	 */
	protected $container;
	/**
	 * @var string
	 */
	protected $base_path;

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @param ContainerInterface $container
	 */
	public function __construct( ContainerInterface $container ) {
		$this->container    = $container;
		$this->base_path    = $this->container['settings']['paths']['upload_dir'];
		$this->current_path = '/' . date( 'Y' ) . '/' . date( 'm' );
		$this->path         = $this->base_path . $this->current_path;
	}

	/**
	 * Renames and moves file to correct location
	 *
	 * @param UploadedFile $file
	 * @param string       $prefix
	 *
	 * @return string Relative path to file.
	 */
	public function upload( UploadedFile $file, $prefix = '' ) {

		$filename = $prefix . uniqid() . '.' . $this->getFileExtension( $file );
		$path     = $this->path . '/' . $filename;

		$this->createPath( $this->path );
		$file->moveTo( $path );

		return $path;
	}

	/**
	 * @param $path
	 */
	public function createPath( $path ) {
		if ( ! is_dir( $path ) ) {
			mkdir( $path, 0766, true );
		}
	}

	/**
	 * @param UploadedFile $file
	 *
	 * @return string
	 */
	public function getFileExtension( UploadedFile $file ) {
		$name = $file->getClientFilename();

		return end( explode( ".", $name ) );
	}
}