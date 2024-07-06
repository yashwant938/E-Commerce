<?php


date_default_timezone_set('Asia/Kolkata');

spl_autoload_register(function ($class_name) {
    $class_file=dirname(dirname(__FILE__)). '/classes/'. $class_name . '.class.php';
    if (file_exists($class_file)) {
        include_once $class_file; 
    }
});


?>
