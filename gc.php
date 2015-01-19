<?php
/*
 * Simple Mage config
 * for checking if yours is loaded
 */


require_once 'app/Mage.php';
Mage::app('admin');

##all the Config ##

/* As Xml */
header('Content-type: text-xml');
echo Mage::getConfig()->getNode()->asXML();
//var_dump(Mage::getConfig()->getNode('modules'));

/* As Array */
//echo '<pre>';
//print_r(Mage::getConfig()->getNode('modules')->asArray());
//echo '</pre>';


