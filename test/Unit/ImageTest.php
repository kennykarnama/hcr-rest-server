<?php

namespace Hcr\Test;

use Hcr\Image;
use Hcr\ImageTypes;

/**
 *
 */
class ImageTest extends \PHPUnit\Framework\TestCase
{
    private $image;
    
    private $imageStream;

    const SAMPLE_IMAGES = array(
        'png'=> array(
            'lena.png'
        )
    );

    const DIR_SAMPLE_IMAGES = 'sample_images';

    const DIR_OUTPUT = 'output_test_image';

    public function setUp()
    {
        $fileName = self::SAMPLE_IMAGES['png'][0];

        $this->image = new Image(
             realpath(
                __DIR__ . '/../'.'/'.
                self::DIR_SAMPLE_IMAGES.
                '/'.$fileName
            )
        );
    }

    public function testInstanceNotNull()
    {
        $this->assertNotNull($this->image);
    }

    public function testImageTypeUndefined()
    {
        $this->assertEquals(ImageTypes::UNDEFINED, $this->image->getImageType());
    }
}
