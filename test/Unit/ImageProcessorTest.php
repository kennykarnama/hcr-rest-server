<?php

namespace Hcr\Test;

use Hcr\ImageProcessor;
use Hcr\ImageTypes;
use Hcr\Configuration;
use Hcr\ConfigMapper;
use Hcr\Models\RgbToBinary;
use Hcr\Models\WordSegmentation;
use Hcr\Models\LineSegmentation;
use Hcr\Models\CharacterSegmentation;
use function Hcr\deleteFiles;

/**
 *
 */
class ImageProcessorTest extends \PHPUnit\Framework\TestCase
{
    const SAMPLE_IMAGES = array(
        'png' => array(
            'lena.png',
            't25.png'
        ),
    );

    const DIR_SAMPLE_IMAGES = 'sample_images';

    const DIR_OUTPUT = 'output_test_image';

    private $configMapper;

    private $imageProcessor;

    private $config;

    private $rgb2binConfig;

    private $lineImages;

    public function setUp()
    {
        $this->config = new Configuration(Configuration::RAW_DECODE);

        $this->configMapper = new configMapper(
            $this->config
        );

        $this->imageProcessor = new ImageProcessor($this->configMapper);
    }


    public function testCreateBinaryImage()
    {
        $fileName = self::SAMPLE_IMAGES['png'][0];
        
        $filePath = realpath(
            __DIR__ . '/../' . '/' .
            self::DIR_SAMPLE_IMAGES .
            '/' . $fileName
        );

        $outpath = getcwd().'/';

        $result = $this->imageProcessor->process(
            [
            RgbToBinary::RGB_TO_BINARY_FIELD,
            $filePath,
            $outpath
            ]
        );

        $fileExist = file_exists($result->getPath());

        $this->assertTrue($fileExist);

        $this->assertEquals($result->getImageType(), ImageTypes::BINARY_IMAGE);
    }
    
    public function testSegmentLine()
    {
        $fileName = self::SAMPLE_IMAGES['png'][1];
        
        $filePath = realpath(
            __DIR__ . '/../' . '/' .
            self::DIR_SAMPLE_IMAGES .
            '/' . $fileName
        );

        $outpath = getcwd().'/';

        $result = $this->imageProcessor->process(
            [
            LineSegmentation::LINE_SEGMENTATION_FIELD,
            $filePath,
            $outpath
            ]
        );

        $this->lineImages = $result;

        $numberImages = count($result);

        $counter = 0;

        foreach ($result as $image) {
            if (file_exists($image->getPath())) {
                $counter++;
            }
        }

        $this->assertEquals($numberImages, $counter);
    }

    public function testSegmentWordTam()
    {
        $lineImages = $this->imageProcessor->process(
            [
                WordSegmentation::WORD_SEGMENTATION_FIELD,
                'line_segmentation.json',
                'paths',
                WordSegmentation::WORD_SEGMENTATION_TAM
            ]
        );
    
        $this->assertNotFalse($lineImages);
    }

    public function testSegmentWordIQM()
    {
        $lineImages = $this->imageProcessor->process(
            [
                WordSegmentation::WORD_SEGMENTATION_FIELD,
                'line_segmentation.json',
                'paths',
                WordSegmentation::WORD_SEGMENTATION_IQM
            ]
        );
    
        $this->assertNotFalse($lineImages);
    }

    public function testSegmentCharacters()
    {
        $test = $this->imageProcessor->process(
            [CharacterSegmentation::CHARACTER_SEGMENTATION_FIELD,
            'word_segmentation.json',
            'paths'
            ]
        );
        print_r($test);
    }

    public static function tearDownAfterClass()
    {
        deleteFiles("*.png");
    }
}
