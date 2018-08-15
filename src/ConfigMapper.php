<?php

namespace Hcr;

use Hcr\Configuration;
use Hcr\Models\LineSegmentation;
use Hcr\Models\WordSegmentation;
use Hcr\Models\RgbToBinary;
use Hcr\Models\CharacterSegmentation;
use Hcr\Models\Recognition;

/**
 *
 */
class ConfigMapper
{
    private $jsonMapper;

    private $config;
    /**
     * Constructor
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->jsonMapper = new \JsonMapper();

        $this->config = $config;
    }
    /**
     * Map json decode to model
     * @param  string $field
     * @return \Hcr\Models\Config
     * @throws \Exception
     */
    public function map(string $field)
    {
        if ($field == LineSegmentation::LINE_SEGMENTATION_FIELD) {
            return $this->jsonMapper->map(
                $this->config->getFieldValue(
                    LineSegmentation::LINE_SEGMENTATION_FIELD
                ),
                new LineSegmentation()
            );
        } elseif ($field == WordSegmentation::WORD_SEGMENTATION_FIELD) {
            return $this->jsonMapper->map(
                $this->config->getFieldValue(
                    WordSegmentation::WORD_SEGMENTATION_FIELD
                ),
                new WordSegmentation()
            );
        } elseif ($field == RgbToBinary::RGB_TO_BINARY_FIELD) {
            return $this->jsonMapper->map(
                $this->config->getFieldValue(
                    RgbToBinary::RGB_TO_BINARY_FIELD
                ),
                new RgbToBinary()
            );
        } elseif ($field == CharacterSegmentation::CHARACTER_SEGMENTATION_FIELD) {
            return $this->jsonMapper->map(
                $this->config->getFieldValue(
                    CharacterSegmentation::CHARACTER_SEGMENTATION_FIELD
                ),
                new CharacterSegmentation()
            );
        } elseif ($field == Recognition::RECOGNITION_FIELD) {
            return $this->jsonMapper->map(
                $this->config->getFieldValue(
                    Recognition::RECOGNITION_FIELD
                ),
                new Recognition()
            );
        }
        throw new \Exception("Unsupported map operation", 1);
    }
}
