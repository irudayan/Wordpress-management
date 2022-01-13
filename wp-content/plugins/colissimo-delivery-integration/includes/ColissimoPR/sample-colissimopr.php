<?php
/**
 * Test with ColissimoPR for 'https://ws.colissimo.fr/pointretrait-ws-cxf/PointRetraitServiceWS/2.0?wsdl'
 * @package ColissimoPR
 * @author WsdlToPhp Team <contact@wsdltophp.fr>
 * @version 20150429-01
 * @date 2018-10-26
 */
ini_set('memory_limit','512M');
ini_set('display_errors',true);
error_reporting(-1);
/**
 * Load autoload
 */
require_once dirname(__FILE__) . '/ColissimoPRAutoload.php';
/**
 * Wsdl instanciation infos. By default, nothing has to be set.
 * If you wish to override the SoapClient's options, please refer to the sample below.
 * 
 * This is an associative array as:
 * - the key must be a ColissimoPRWsdlClass constant beginning with WSDL_
 * - the value must be the corresponding key value
 * Each option matches the {@link http://www.php.net/manual/en/soapclient.soapclient.php} options
 * 
 * Here is below an example of how you can set the array:
 * $wsdl = array();
 * $wsdl[ColissimoPRWsdlClass::WSDL_URL] = 'https://ws.colissimo.fr/pointretrait-ws-cxf/PointRetraitServiceWS/2.0?wsdl';
 * $wsdl[ColissimoPRWsdlClass::WSDL_CACHE_WSDL] = WSDL_CACHE_NONE;
 * $wsdl[ColissimoPRWsdlClass::WSDL_TRACE] = true;
 * $wsdl[ColissimoPRWsdlClass::WSDL_LOGIN] = 'myLogin';
 * $wsdl[ColissimoPRWsdlClass::WSDL_PASSWD] = '**********';
 * etc....
 * Then instantiate the Service class as: 
 * - $wsdlObject = new ColissimoPRWsdlClass($wsdl);
 */
/**
 * Examples
 */


/************************************
 * Example for ColissimoPRServiceFind
 */
$colissimoPRServiceFind = new ColissimoPRServiceFind();
// sample call for ColissimoPRServiceFind::findRDVPointRetraitAcheminementByToken()
if($colissimoPRServiceFind->findRDVPointRetraitAcheminementByToken(new ColissimoPRStructFindRDVPointRetraitAcheminementByToken(/*** update parameters list ***/)))
    print_r($colissimoPRServiceFind->getResult());
else
    print_r($colissimoPRServiceFind->getLastError());
// sample call for ColissimoPRServiceFind::findRDVPointRetraitAcheminement()
if($colissimoPRServiceFind->findRDVPointRetraitAcheminement(new ColissimoPRStructFindRDVPointRetraitAcheminement(/*** update parameters list ***/)))
    print_r($colissimoPRServiceFind->getResult());
else
    print_r($colissimoPRServiceFind->getLastError());
// sample call for ColissimoPRServiceFind::findPointRetraitAcheminementByID()
if($colissimoPRServiceFind->findPointRetraitAcheminementByID(new ColissimoPRStructFindPointRetraitAcheminementByID(/*** update parameters list ***/)))
    print_r($colissimoPRServiceFind->getResult());
else
    print_r($colissimoPRServiceFind->getLastError());
