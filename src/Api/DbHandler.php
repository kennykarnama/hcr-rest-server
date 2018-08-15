<?php

namespace Hcr\Api;

use \Filebase\Database;
use \RandomLib\Factory;

/**
 * Class to build resource
 * needed to interact
 * with fileBase
 * and random generator
 */
class DbHandler
{
    protected $fileBase;

    protected $randomFactory;

    protected $randGenerator;
    /**
     * Constructor
     * @param string|null $dbPath
     */
    public function __construct($dbPath = null)
    {
        $this->randomFactory = new Factory();

        $this->randGenerator = $this->randomFactory->getMediumStrengthGenerator();

        $this->fileBase = new Database(
            ['dir'=>$dbPath]
        );
    }
}
