<?php

namespace Hcr;

/**
 *
 */
class Configuration
{
    private $config;

    private $contents;

    const CONFIG_NAME = 'config.json';

    const RAW_DECODE = "raw";

    const STRINGIFY_DECODE = "stringify";

    /**
     * Constructor
     * @param mixed $args
     */
    public function __construct($args = '', $configPath = '')
    {
        if (empty($configPath)) {
            $configPath = getcwd() . '/' . self::CONFIG_NAME;
        }
        $this->contents = file_get_contents(
            $configPath
        );

        $this->setupConfig($args);
    }
    /**
     * Setup config
     * @param  mixed $args
     * @return void
     */
    private function setupConfig($args)
    {
        if (is_string($args)) {
            if ($args == self::STRINGIFY_DECODE || $args == '') {
                $this->config = $this->decodeJson(
                    $this->contents,
                    true
                );
            } elseif ($args == self::RAW_DECODE) {
                $this->config = $this->decodeJson(
                    $this->contents,
                    false
                );
            }
        }
    }
    /**
     * Json decode content
     * @param  mixed $contents
     * @param  bool $mode
     * @return array
     */
    private function decodeJson($contents, $mode)
    {
        return json_decode(
            $contents,
            $mode
        );
    }
    /**
     * Get config
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
    /**
     * Get prop value
     * @param  mixed $field
     * @return mixed
     */
    public function getFieldValue($field)
    {
        if (is_object($this->config)) {
            if (property_exists($this->config, $field)) {
                return $this->config->$field;
            }
            throw new \Exception("Undefined property", 1);
        } elseif (is_array($this->config)) {
            if (isset($this->config[$field])) {
                return $this->config[$field];
            }
            throw new \Exception("Error Processing Request", 1);
        }
        throw new \Exception("Undefined type", 1);
    }
    public function getContents()
    {
        return $this->contents;
    }
}
