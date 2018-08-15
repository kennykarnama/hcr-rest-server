<?php

namespace Hcr\Api;

use Psr\Http\Message\ServerRequestInterface;

/**
 *
 */
class ImageHandler
{
    const DEFAULT_EXTENSION = '.png';

    /**
     * Retrieve image
     * from json file
     * @param  string  $jsonFilePath
     * @param  boolean $getPath
     * @return array
     */
    public function retrieveImageFromJson($jsonFilePath = '', $getPath = false)
    {
        $contents = file_get_contents($jsonFilePath);

        $decoded = json_decode($contents, true);

        $imagePaths = $decoded['paths'];

        if (!$getPath) {
            $images = array();

            foreach ($imagePaths as $imagePath) {
                array_push(
                    $images,
                    file_get_contents($imagePath)
                );
            }
        }
        return $imagePaths;
    }
    /**
     * Handle image retrieval by name
     * @param  string $scannedPath
     * @param  string $fileName
     * @return string
     */
    public function handleRetrieval(string $scannedPath = '', string $fileName = '', $getPath = false)
    {
        $image = $this->searchFileByName($scannedPath, $fileName);

        if ($getPath) {
            return $image;
        }

        $image = file_get_contents($image);

        if (!$image) {
            throw new \Exception("Image not found", 1);
        }

        return $image;
    }

    /**
     * Handle file upload
     * @param  ServerRequestInterface $request
     * @param  string                 $field
     * @param  bool|boolean           $isMoved
     * @param  string                 $movedPath
     * @return string
     */
    public function handleUpload(
        ServerRequestInterface $request,
        string $field,
        bool $isMoved = false,
        string $movedPath = ""
    ) {
        $files = $request->getUploadedFiles();

        $key = $request->getParsedBody()['key'];

        $newFile = $this->filterFileExistence(
            $files,
            $field
        );

        if ($isMoved) {
            return $this->moveFile($newFile, $key, $movedPath);
        }
    }
    /**
     * Search file by name
     * @param  string $scannedPath
     * @param  string $name
     * @return array
     * @throws \Exception
     */
    private function searchFileByName(string $scannedPath = '', string $name = '')
    {
        $files = glob($scannedPath . DIRECTORY_SEPARATOR . $name . "*");

        if (count($files) > 1) {
            throw new \Exception("Multiple image request", 1);
        } elseif (count($files) == 0) {
            throw new \Exception("Image not found", 1);
        } else {
            return current($files);
        }
    }
    /**
     * Filter file existence
     * @param  array  $file
     * @param  string $field
     * @return mixed
     */
    private function filterFileExistence(array $file, string $field)
    {
        if (!empty($file[$field])) {
            return $file[$field];
        }
        throw new \Exception("Expected file", 1);
    }
    /**
     * Move file to specific
     * directory, after uploaded
     * @param  mixed $file
     * @param  string $key
     * @param  string $movedPath
     * @return string
     */
    private function moveFile($file, $key, $movedPath)
    {
        if ($file->getError() == UPLOAD_ERR_OK) {
            $extension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
            $filename  = $key . "." . $extension;

            $file->moveTo($movedPath . DIRECTORY_SEPARATOR . $filename);

            return $filename;
        }
        throw new \Exception("Error uploading file", 1);
    }
}
