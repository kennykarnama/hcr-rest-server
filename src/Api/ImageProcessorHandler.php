<?php

namespace Hcr\Api;

use Hcr\Models\LineSegmentation;
use Hcr\Models\WordSegmentation;
use Hcr\Models\CharacterSegmentation;
use Hcr\Models\RgbToBinary;
use \Hcr\ConfigMapper;
use \Hcr\Configuration;
use \Hcr\ImageProcessor;

/**
 *
 */
class ImageProcessorHandler
{
    private $config;
    private $configMapper;
    private $imageProcessor;
    /**
     * Constructor
     * @param string $configPath
     */
    public function __construct($configPath = '')
    {
        $this->config = new Configuration(
            Configuration::RAW_DECODE,
            $configPath
        );

        $this->configMapper = new configMapper(
            $this->config
        );

        $this->imageProcessor = new ImageProcessor(
            $this->configMapper
        );
    }
    /**
     * Convert to RGB image to binary
     * @param  string $imagePath
     * @param  string $outPath
     * @param  string $programPath
     * @return \Hcr\Image
     */
    public function convertToBinary($imagePath = '', $outPath = '', $programPath = '')
    {
        $result = $this->imageProcessor->process(
            [
                RgbToBinary::RGB_TO_BINARY_FIELD,
                $imagePath,
                $outPath,
                $programPath,
            ]
        );

        return $result;
    }
    /**
     * Segment lines based on previously
     * uploaded image
     * @param  string $imagePath
     * @param  string $outPath
     * @param  string $prefixFileName
     * @param  string $programPath
     * @return array|string
     */
    public function segmentLines($imagePath = '', $outPath = '', $prefixFileName = '', $programPath = '')
    {
        $result = $this->imageProcessor->process(
            [
                LineSegmentation::LINE_SEGMENTATION_FIELD,
                $imagePath,
                $outPath,
                $prefixFileName,
                $programPath,
            ]
        );

        $lines = array();

        if (is_iterable($result)) {
            foreach ($result as $item) {
                array_push(
                    $lines,
                    $item->getData()
                );
            }
            return $lines;
        }

        return $result;
    }
    /**
     * Segment words
     * Till the end of line images
     * @param  string $lineSegmentationConfig
     * @param  string $pathField
     * @param  string $wordSegmentationType
     * @param  string $programPath
     * @param  string $outpath
     * @return array
     */
    public function segmentWords(
        $lineSegmentationConfig = '',
        $pathField = '',
        $wordSegmentationType = '',
        $programPath = '',
        $outpath = '',
        $prefixFileName = ''
    ) {
        $wordImages = $this->imageProcessor->process(
            [
                WordSegmentation::WORD_SEGMENTATION_FIELD,
                $lineSegmentationConfig,
                $pathField,
                $wordSegmentationType,
                $programPath,
                $outpath,
                $prefixFileName
            ]
        );
        
        return $wordImages;
    }
    /**
     * Segment characters
     * @param  string $wordSegmentationConfig
     * @param  string $pathField
     * @param  string $programPath
     * @param  string $outPath
     * @param  string $prefixFileName
     * @return array
     */
    public function segmentCharacters(
        $wordSegmentationConfig = '',
        $pathField = '',
        $programPath = '',
        $outPath = '',
        $prefixFileName = ''
    ) {
        $characters = $this->imageProcessor->process(
            [CharacterSegmentation::CHARACTER_SEGMENTATION_FIELD,
            $wordSegmentationConfig,
            $pathField,
            $programPath,
            $outPath,
            $prefixFileName
            ]
        );

        return $characters;
    }
    /**
     * Get config contents
     * @return array
     */
    public function getConfigContents()
    {
        return $this->config->getContents();
    }
}
