<?php

namespace Hcr\Test;

use Hcr\Configuration;
use Hcr\Models\LineSegmentation;
use Hcr\Models\WordSegmentation;

/**
 *
 */
class JsonMapperTest extends \PHPUnit\Framework\TestCase
{
    private $jsonMapper;
    private $config;

    public function setUp()
    {
        $this->jsonMapper = new \JsonMapper();
        $this->config     = new Configuration(Configuration::RAW_DECODE);
    }

    public function testInstanceNotNull()
    {
        $this->assertNotNull($this->jsonMapper);
    }

    public function testMapToLineSegmentation()
    {
        $LineSegmentationConfig = $this->config->getFieldValue(
            'line_segmentation'
        );

        $lineSegmentation = $this->jsonMapper->map(
            $LineSegmentationConfig,
            new LineSegmentation()
        );

        $this->assertNotNull($lineSegmentation);

        $this->assertInstanceOf(LineSegmentation::class, $lineSegmentation);
    }

    public function testMapToWordSegmentation()
    {
        $wordSegmentationOnConfig = $this->config->getFieldValue(
            'word_segmentation'
        );
        $wordSegmentation = $this->jsonMapper->map(
            $wordSegmentationOnConfig,
            new WordSegmentation()
        );
        $this->assertNotNull($wordSegmentation);

        $this->assertInstanceOf(WordSegmentation::class, $wordSegmentation);
    }
}
