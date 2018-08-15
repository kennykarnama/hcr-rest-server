<?php

namespace Hcr\Test;

use Hcr\Configuration;
use Hcr\ConfigMapper;
use Hcr\Models\Config;
use Hcr\Models\LineSegmentation;
use Hcr\Models\WordSegmentation;
use Hcr\Models\RgbToBinary;
use Hcr\Models\CharacterSegmentation;
use Hcr\Models\Recognition;

/**
 *
 */
class ConfigMapperTest extends \PHPUnit\Framework\TestCase
{
    private $configMapper;
    private $config;

    public function setUp()
    {
        $this->config = new Configuration(
            Configuration::RAW_DECODE
        );

        $this->configMapper = new ConfigMapper(
            $this->config
        );
    }

    public function testInstanceNotNull()
    {
        $this->assertNotNull(
            $this->config
        );
        $this->assertNotNull(
            $this->configMapper
        );
    }

    public function testMapLineSegmentation()
    {
        $lineSegmentation = $this->configMapper->map(
            LineSegmentation::LINE_SEGMENTATION_FIELD
        );
        $this->assertNotNull($lineSegmentation);

        $this->assertInstanceOf(Config::class, $lineSegmentation);

        $this->assertInstanceOf(LineSegmentation::class, $lineSegmentation);
    }

    public function testMapWordSegmentation()
    {
        $wordSegmentation = $this->configMapper->map(
            WordSegmentation::WORD_SEGMENTATION_FIELD
        );

        $this->assertNotNull($wordSegmentation);

        $this->assertInstanceOf(Config::class, $wordSegmentation);

        $this->assertInstanceOf(WordSegmentation::class, $wordSegmentation);
    }

    public function testMapRgbToBinary()
    {
        $rgbtoBinary = $this->configMapper->map(
            RgbToBinary::RGB_TO_BINARY_FIELD
        );

        $this->assertNotNull($rgbtoBinary);

        $this->assertInstanceOf(Config::class, $rgbtoBinary);

        $this->assertInstanceOf(RgbToBinary::class, $rgbtoBinary);
    }

    public function testMapCharacterSegmentation()
    {
        $characterSegmentation = $this->configMapper->map(
            CharacterSegmentation::CHARACTER_SEGMENTATION_FIELD
        );

        $this->assertNotNull($characterSegmentation);

        $this->assertInstanceOf(Config::class, $characterSegmentation);

        $this->assertInstanceOf(CharacterSegmentation::class, $characterSegmentation);
    }

    public function testMapRecognition()
    {
        $recognition = $this->configMapper->map(
            Recognition::RECOGNITION_FIELD
        );

        $this->assertNotNull($recognition);

        $this->assertInstanceOf(Config::class, $recognition);

        $this->assertInstanceOf(Recognition::class, $recognition);
    }
}
