<?php

require_once realpath(__DIR__ . '/..' . '/..' . '/vendor/autoload.php');

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use \Hcr\Api\AbbreviationHandler;
use \Hcr\Api\ImageHandler;
use \Hcr\Api\ImageProcessorHandler;
use Hcr\Models\WordSegmentation;

$app = new Slim\App;

$container = $app->getContainer();
$container['upload_directory'] = __DIR__ . '/uploaded_images';
$container['binary_output_directory'] = __DIR__.'/binary_output_image';
$container['lines_output_directory'] = __DIR__.'/lines_output_image';
$container['words_output_directory'] = __DIR__.'/words_output_image';
$container['characters_output_directory'] = __DIR__.'/characters_output_image';
$container['base_app_directory'] = __DIR__.'/..'.'/..';

$abbreviationHandler = new AbbreviationHandler();

$imageHandler = new ImageHandler();

$imageProcessorHandler = new ImageProcessorHandler(
    realpath(__DIR__.'/..'.'/..').'/config.json'
);


$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});



/**
 * About this project
 */
$app->get('/about', function (ServerRequestInterface $request, ResponseInterface $response) {
    $data = array(
        'author'  => 'kennykarnama@gmail.com',
        'github'  => 'github.com/kennykarnama',
        'license' => 'MIT',
        'version' => '1.0',
    );

    return $response->withJson(
        $data,
        201
    );
});

/**
 * Uncommon abbreviations
 */
$app->post('/uncommon_abbreviation', function (
    ServerRequestInterface $request,
    ResponseInterface $response
) use ($abbreviationHandler) {
    $newAbbreviation = $request->getParsedBody();

    return $response->withJson(
        $abbreviationHandler->acceptRequest(
            AbbreviationHandler::CREATE_ABBREVIATION,
            $newAbbreviation
        ),
        200
    );
});

$app->put('/uncommon_abbreviation/update/meaning/{abbreviation}', function (
    ServerRequestInterface $request,
    ResponseInterface $response,
    $args
) use ($abbreviationHandler) {
    $newAbbreviation = $request->getParsedBody();
    return $response->withJson(
        $abbreviationHandler->acceptRequest(
            AbbreviationHandler::UPDATE_ABBREVIATION_MEANING,
            $newAbbreviation
        ),
        200
    );
});

$app->put('/uncommon_abbreviation/append/meaning/{abbreviation}', function (
    ServerRequestInterface $request,
    ResponseInterface $response,
    $args
) use ($abbreviationHandler) {
    $newAbbreviation = $request->getParsedBody();
    return $response->withJson(
        $abbreviationHandler->acceptRequest(
            AbbreviationHandler::APPEND_ABBREVIATION_MEANING,
            $newAbbreviation
        ),
        200
    );
});

$app->delete('/uncommon_abbreviation/{abbreviation}', function (
    ServerRequestInterface $request,
    ResponseInterface $response,
    $args
) use ($abbreviationHandler) {
    return $response->withJson(
        $abbreviationHandler->acceptRequest(
            AbbreviationHandler::DELETE_ABBREVIATION,
            $args['abbreviation']
        ),
        201
    );
});

$app->get('/uncommon_abbreviation', function (
    ServerRequestInterface $request,
    ResponseInterface $response
) use ($abbreviationHandler) {
    return $response->withJson(
        $abbreviationHandler->acceptRequest(
            AbbreviationHandler::FETCH_ABBREVIATIONS
        ),
        201
    );
});

$app->get('/uncommon_abbreviation/{abbreviation}', function (
    ServerRequestInterface $request,
    ResponseInterface $response,
    $args
) use ($abbreviationHandler) {
    return $response->withJson(
        $abbreviationHandler->acceptRequest(
            AbbreviationHandler::GET_ABBREVIATION,
            $args['abbreviation']
        ),
        201
    );
});

$app->post('/image', function (
    ServerRequestInterface $request,
    ResponseInterface $response,
    $args
) use ($imageHandler) {
    $directory = $this->get('upload_directory');
    return $response->withJson(
            $imageHandler->handleUpload(
            $request,
            "sample_image",
            true,
            $directory
            ),
            201
        );
});

$app->post('/image_processor/convert_to_binary', function (
    ServerRequestInterface $request,
    ResponseInterface $response,
    $args
) use ($imageProcessorHandler,$imageHandler) {
    $key = $request->getParsedBody()['key'];
    
    $filePath = $imageHandler->handleRetrieval(
        $this->get('upload_directory'),
        $key,
        true
    );

    $outFileName = $key.'_';

    $result = $imageProcessorHandler->convertToBinary(
        $filePath,
        $this->get('binary_output_directory').DIRECTORY_SEPARATOR.$outFileName,
        $this->get('base_app_directory')

    );
    return $response->withJson(
        $result->getData(),
        201
    );
});

$app->post('/image_processor/segment_lines', function (
    ServerRequestInterface $request,
    ResponseInterface $response,
    $args
) use ($imageProcessorHandler,$imageHandler) {
    $key = $request->getParsedBody()['key'];

    $filePath = $imageHandler->handleRetrieval(
        $this->get('binary_output_directory'),
        $key,
        true
    );
    
    $prefixFileName = $key.'_';

    $lines = $imageProcessorHandler->segmentLines(
        $filePath,
        $this->get('lines_output_directory').DIRECTORY_SEPARATOR,
        $prefixFileName,
        $this->get('base_app_directory')
    );

    return $response->withJson(
            $lines,
            201
        );
});

$app->post('/image_processor/segment_words', function (
    ServerRequestInterface $request,
    ResponseInterface $response,
    $args
) use ($imageProcessorHandler,$imageHandler) {
    $key = $request->getParsedBody()['key'];
    
    $jsonPath = $this->get('lines_output_directory').DIRECTORY_SEPARATOR.'line_segmentation.json';

    $prefixFileName  = $key.'_';
    $wordImages = $imageProcessorHandler->segmentWords(
        $jsonPath,
        'paths',
        WordSegmentation::WORD_SEGMENTATION_TAM,
        $this->get('base_app_directory'),
        $this->get('words_output_directory'),
        $prefixFileName

    );

    return $response->withJson(
        $wordImages,
        201
    );
});

$app->post('/image_processor/segment_characters', function (
    ServerRequestInterface $request,
    ResponseInterface $response,
    $args
) use ($imageProcessorHandler,$imageHandler) {
    $key = $request->getParsedBody()['key'];
    
    $jsonPath = $this->get('words_output_directory').DIRECTORY_SEPARATOR.'word_segmentation.json';

    $prefixFileName  = $key.'_';

    $characterImages = $imageProcessorHandler->segmentCharacters(
        $jsonPath,
        'paths',
        $this->get('base_app_directory'),
        $this->get('characters_output_directory'),
        $prefixFileName

    );

    return $response->withJson(
        $characterImages,
        201
    );
});

$app->get('/image/{imageFile}', function (
    ServerRequestInterface $request,
    ResponseInterface $response,
    $args
) use ($imageHandler) {
    $fileName = $args['imageFile'];

    $image = $imageHandler->handleRetrieval(
            $this->get('upload_directory'),
            $fileName
        );

    $response->write($image);

    return $response->withHeader(
            'Content-Type',
            FILEINFO_MIME_TYPE
        );
});


$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});

$app->run();
