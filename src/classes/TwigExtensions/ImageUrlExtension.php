<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 11/02/17
 */

namespace App\TwigExtensions;


use App\Entities\ImageEntity;
use App\Models\ImageModel;
use Interop\Container\ContainerInterface;

class ImageUrlExtension extends \Twig_Extension {
	/**
	 * @var ImageModel
	 */
	public $imageModel;

	/**
	 * @param ContainerInterface $c
	 */
	public function __construct( ContainerInterface $c ) {
		$this->imageModel  = $c->get( 'ImageModel' );
	}

	public function getFunctions() {
		return [
			new \Twig_SimpleFunction( 'image_url', array( $this, 'imageUrl' ) ),
		];
	}

	/**
	 * @param        $image
	 * @param string $size
	 *
	 * @return string
	 */
	public function imageUrl( $image, $size = '' ) {
		return $this->imageModel->getImageUrl( new ImageEntity( $image ), $size );
	}
}