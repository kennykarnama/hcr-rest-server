<?php

namespace Hcr;

use Hcr\Image;
use Hcr\ConfigMapper;
use Hcr\ImageTypes;
use Hcr\Models\CharacterSegmentation;
use Hcr\Models\LineSegmentation;
use Hcr\Models\RgbToBinary;
use Hcr\Models\WordSegmentation;

/**
 * Image Processor
 */
class ImageProcessor
{
    private $configMapper;

    /**
     * Constructor
     * @param configMapper $configMapper
     */
    public function __construct(
        configMapper $configMapper
    ) {
        $this->configMapper = $configMapper;
    }
    /**
     * Process image using image processing algorithms
     * @param  mixed $args
     * @return mixed
     * @throws \Exception
     */
    public function process($args)
    {
        $request = array_shift($args);

        switch ($request) {
            case RgbToBinary::RGB_TO_BINARY_FIELD:
                return $this->createBinaryImage(
                    $this->configMapper->map(
                        $request
                    ),
                    $args
                );
            case LineSegmentation::LINE_SEGMENTATION_FIELD:
                return $this->segmentLine(
                    $this->configMapper->map(
                        $request
                    ),
                    $args
                );
            case WordSegmentation::WORD_SEGMENTATION_FIELD:
                return $this->segmentWords(
                    $this->configMapper->map(
                        $request
                    ),
                    $args
                );
            case CharacterSegmentation::CHARACTER_SEGMENTATION_FIELD:
                return $this->segmentCharacters(
                    $this->configMapper->map(
                        $request
                    ),
                    $args
                );
            default:
                throw new \Exception("Unsupported request", 1);

        }
    }
    /**
     * Create binary image
     * @param  Configuration $config
     * @param  array  $args
     * @return \Hcr\Image
     */
    private function createBinaryImage($config, $args = array())
    {
        /**
         * Get sub program name to run line segmentation
         * @var string
         */
        $appName = $config->appName;
        /**
         * Image path to be segmentated
         * @var string
         */
        $filePath = $args[0];
        /**
         * Outpath to write png as result of line segmentation
         * @var string
         */
        $outPath = $args[1];
        /**
         * Path to c++ program (line_segmentation)
         * @var string
         */
        if (empty($args[2])) {
            $args[2] = getcwd();
        }
        $pathProgram = $args[2].DIRECTORY_SEPARATOR.'opencv'.DIRECTORY_SEPARATOR.$appName;
        /**
         * string as result of c++ program
         * if run successfully, will return
         * path1,path2,..
         * If error, will return error
         * Or other exception message
         * @var string
         */
        $result = exec($pathProgram . ' ' . $filePath . ' ' . $outPath);

        $binaryImage = new Image($result, ImageTypes::BINARY_IMAGE);

        return $binaryImage;
    }
    /**
     * Segment text lines
     * @param  mixed $config
     * @param  array         $args
     * @return array
     */
    private function segmentLine($config, array $args)
    {
        /**
         * Get sub program name to run line segmentation
         * @var string
         */
        $appName = $config->appName;
        /**
         * Image path to be segmentated
         * @var string
         */
        $filePath = $args[0];
        /**
         * Out file as the result of line segmentation
         * @var string
         */
        $fileName = $config->fileName;
        /**
         * Outpath to write png as result of line segmentation
         * @var string
         */
        $outPath = $args[1];
        /**
         * Options to do line segmentation
         * send as extra argument for c++ program
         * @var
         */
        $options = $config->args[0];
        /**
         * Path to c++ program (line_segmentation)
         * @var string
         */
        if (empty($args[3])) {
            $args[3] = getcwd();
        }
        $pathProgram = $args[3] . DIRECTORY_SEPARATOR . 'opencv' . DIRECTORY_SEPARATOR . $appName;
        /**
         * string as result of c++ program
         * if run successfully, will return
         * path1,path2,..
         * If error, will return error
         * Or other exception message
         * @var string
         */
        $result = exec(
            $pathProgram
            . ' ' .
            $filePath
            . ' ' .
            $outPath.$args[2].$fileName
            . ' ' .
            $options
            . ' ' .
            $outPath
        );
        /**
         * Path to write JSOn
         * @var string
         */
        $outfilePath = $outPath;
        /**
         * Initial value of json array paths
         * @var array
         */
        $jsonResult = array(
            'paths' => explode(",", $result),
        );
        /**
         * Write to file
         */
        $this->writeToFile(
            $outPath.$config->outFile,
            $jsonResult
        );
        /**
         * Return array of binary images
         */
        return $this->createBinImages(
            $result,
            ImageTypes::BINARY_IMAGE
        );
    }
    /**
     * Segment words
     * @param  mixed $config
     * @param  array         $args
     * @return array
     */
    private function segmentWords($config, array $args)
    {
        $lineImages = $this->decodeJson($args[0])[$args[1]];

        $result = array();

        $indexOperation;

        switch ($args[2]) {
            case 'tam':
                $indexOperation = 0;
                break;
            case 'iqm':
                $indexOperation = 1;
                break;
            default:
                throw new \Exception("Operation not supported", 1);
        }

        $options = $config->args[$indexOperation];

        $appName = $config->appName;

        if (empty($args[3])) {
            $args[3] = getcwd();
        }

        $pathProgram = $args[3] . DIRECTORY_SEPARATOR . 'opencv' . DIRECTORY_SEPARATOR . $appName;

        $outfileName = $config->fileName;

        $jsonResults = array();

        $row         = 1;
        
        foreach ($lineImages as $lineImagePath) {
            $dirName     = $this->removeFileExtension($lineImagePath, ".png");
            $outfilePath = $row++;
            $result      = exec(
                $pathProgram
                . ' ' .
                $lineImagePath
                . ' ' .
                $args[4].'/'.$args[5].$outfilePath
                . ' ' .
                $options
            );
            $jsonResult = array(
                'paths' => explode(",", $result),
            );

            array_push($jsonResults, $jsonResult);
        }

        $status = $this->writeToFile(
            $args[4].'/'.$config->outFile,
            $jsonResults
        );

        if ($status) {
            return $jsonResults;
        }

        throw new \Exception("Unable to write json file", 1);
    }
    /**
     * Segment non cursive character using
     * Zero vertical projection
     * Remember, file of word_segmentation.json
     * is in multi array form
     * @param  mixed $config
     * @param  array         $args
     * @return array
     */
    private function segmentCharacters($config, array $args)
    {
        $wordImages = $this->decodeJson($args[0]);

        $appName = $config->appName;

        if (empty($args[2])) {
            $args[2] = getcwd();
        }

        $pathProgram = $args[2] . DIRECTORY_SEPARATOR . 'opencv' . DIRECTORY_SEPARATOR . $appName;

        $options = $config->args[0];

        $outfileName = $args[3].DIRECTORY_SEPARATOR.$config->fileName;

        $row  = 0;
        $word = 0;

        $jsonResults = array();

        foreach ($wordImages as $wordImage) {
            $wordImagePath = $wordImage[$args[1]];

            $n = count($wordImagePath);

            $outfilePath = $args[3].DIRECTORY_SEPARATOR.$args[4].'Baris_'.$row.'_kata_';

            $jsonWordResult = [];

            for ($word = 0; $word < $n; $word++) {
                $outfilePath.=$word;

                $result      = shell_exec(
                    $pathProgram
                    . ' ' .
                    $wordImagePath[$word]
                    . ' ' .
                    $outfilePath
                    .' '.
                    $options
                );
                $jsonResult = array(
                    'paths' => explode(",", $result),
                );

                $jsonWordResult[] = $jsonResult;
            }

            

            array_push($jsonResults, $args[3].DIRECTORY_SEPARATOR.$config->outFile.'_'.$row);

            $this->writeToFile(
                    $args[3].DIRECTORY_SEPARATOR.$config->outFile.'_'.$row,
                    $jsonWordResult
            );

            $row++;
        }

        return $jsonResults;
    }
    /**
     * Create images based on array or string
     * of paths with delimiter ","
     * @param  array|string $paths
     * @return array
     */
    private function createBinImages($paths, $imageType)
    {
        if (is_string($paths)) {
            $paths = explode(",", $paths);
        }
        $images = array();
        foreach ($paths as $path) {
            $image = new Image($path, $imageType);

            array_push($images, $image);
        }
        return $images;
    }
    /**
     * Write contens to file
     * @param  string $outpath
     * @param  string $contents
     * @return int|bool
     */
    private function writeToFile($outpath, $contents)
    {
        if (is_array($contents)) {
            $contents = json_encode($contents);
        }

        $fp = fopen($outpath, 'wa+');

        $status = fwrite($fp, $contents);

        fclose($fp);

        return $status;
    }
    /**
     * Decode json from given path
     * @param  string $path
     * @return array
     */
    private function decodeJson($path)
    {
        $decoded = json_decode(file_get_contents($path), true);

        return $decoded;
    }
    /**
     * Remove file extension from given string
     * @param  string $str
     * @param  string $ext
     * @return string
     */
    private function removeFileExtension($str, $ext)
    {
        return str_replace($ext, "", $str);
    }
}
