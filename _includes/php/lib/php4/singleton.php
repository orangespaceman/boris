<?php 
/**
 * This class imitates Singleton behaviour for PHP4
 * From: http://www.weberdev.com/get_example-4014.html
 */
 
 function &singleton($class) {
     static $instances;

     if (!is_array($instances)) {
         $instances = array();
     }

     if (!isset($instances[$class])) {
         $instances[$class] = new $class;
     }

     return $instances[$class];
 }
?>