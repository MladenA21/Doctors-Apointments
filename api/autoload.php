<?php
    function autoloader($class_name){
        $file = __DIR__.'/includes/'.$class_name.'.php';
    
        if ( file_exists($file) ) {
            require_once $file;
        }
    }
    
    spl_autoload_register('autoloader');

