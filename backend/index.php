<?php
define("ROOT_PATH", __DIR__);
require_once(ROOT_PATH.'/config.php');

function load_class($class_name) {
    $items = explode('\\', strtolower($class_name));
    foreach(array('lib', 'modules') as $item) {
        $path = implode(DIRECTORY_SEPARATOR, array_merge(array($item), $items)).'.php';
        if(file_exists($path)) {
            require_once $path;
            return true;
        }
    }
}

spl_autoload_register('load_class');

try {
    $method = Utils::Request('method');
    $method = explode('.', $method);
    if(sizeof($method) == 2) {
        $class_name = 'API\\'.$method[0];
        if(load_class($class_name) && class_exists($class_name)) {
            $instance = new $class_name;
            $method = $method[1];
            if(method_exists($instance, $method)) {
                $res = $instance->$method();
            } else {
                throw new Exception('Unknown method');
            }
        } else {
            throw new Exception('Unknown class');
        }
    } else {
        throw new Exception('Invalid method');
    }
} catch(Exception $e) {
    $res = array(
        'error' => $e->getMessage(),
    );
}

echo json_encode($res);
