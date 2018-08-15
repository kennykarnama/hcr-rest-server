<?php

namespace Hcr\Test;

use Hcr\ImageStream;

/**
 *
 */
class ImageStreamTest extends \PHPUnit\Framework\TestCase
{
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
        $this->imageStream = new ImageStream();
    }

    public function testInstanceNotNull()
    {
        $this->assertNotNull($this->imageStream);
    }

    public function testReadPngImage()
    {
        $fileName = self::SAMPLE_IMAGES['png'][0];

        $image = $this->imageStream->in(
            realpath(
                __DIR__ . '/../'.'/'.
                self::DIR_SAMPLE_IMAGES.
                '/'.$fileName
            ),
            ['ext'=>'.png']
        );

        $this->assertEquals(
            'gd',
            get_resource_type($image)
        );
    }
    /**
     * @expectedException \Exception
     */
    public function testReadImageFail()
    {
        $fileName = self::SAMPLE_IMAGES['png'][0];

        $image = $this->imageStream->in(
            realpath(
                __DIR__ . '/../'.'/'.
                self::DIR_SAMPLE_IMAGES.
                '/'.$fileName
            ),
            ['ext'=>'.jpg']
        );
    }

    public function testWritePngImage()
    {
        $fileName = self::SAMPLE_IMAGES['png'][0];

        $image = $this->imageStream->in(
            realpath(
                __DIR__ . '/../'.'/'.
                self::DIR_SAMPLE_IMAGES.
                '/'.$fileName
            ),
            ['ext'=>'.png']
        );

        $this->assertEquals(
            'gd',
            get_resource_type($image)
        );

        $outFile = "out.png";

        $status = $this->imageStream->out(
            
             realpath(
                __DIR__ . '/../'.'/'.
                self::DIR_OUTPUT.
                '/'
            ) . '/'.$outFile,
             $image,
            ['ext'=>'.png']
        );

        $this->assertTrue($status);
    }
}
