<?php
namespace hrgruri\bot;

class Config
{
    const CONFIG_FILE_PATH = '../data/config.json';
    private static $instance;
    private $data;
    private function __construct()
    {
        $this->load();
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function load()
    {
        $config_file_path = __DIR__ . '/' . self::CONFIG_FILE_PATH;
        if (!file_exists($config_file_path)) {
            throw new \Exception('Configuration file does not exist');
        }
        $config = json_decode(file_get_contents($config_file_path));
        if (is_null($config)) {
            throw new \Exception('Configuration file format is broken.');
        }
        $this->data = $config;
    }

    public function get(string $key)
    {
        if (isset($this->data->{$key})){
            return $this->data->{$key};
        }
        throw new \Exception("Configuration {$key} does not exist");
    }
}
