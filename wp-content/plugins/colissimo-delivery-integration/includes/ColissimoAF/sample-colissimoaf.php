<?php
/**
 * Test with ColissimoAF for 'http://ws.Colissimo.fr/sls-ws/SlsServiceWS?wsdl'
 * @package ColissimoAF
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2018-10-26
 */
ini_set('memory_limit','512M');
ini_set('display_errors',true);
error_reporting(-1);
/**
 * Load autoload
 */
require_once dirname(__FILE__) . '/ColissimoAFAutoload.php';
/**
 * Wsdl instanciation infos. By default, nothing has to be set.
 * If you wish to override the SoapClient's options, please refer to the sample below.
 * 
 * This is an associative array as:
 * - the key must be a ColissimoAFWsdlClass constant beginning with WSDL_
 * - the value must be the corresponding key value
 * Each option matches the {@link http://www.php.net/manual/en/soapclient.soapclient.php} options
 * 
 * Here is below an example of how you can set the array:
 * $wsdl = array();
 * $wsdl[ColissimoAFWsdlClass::WSDL_URL] = 'http://ws.Colissimo.fr/sls-ws/SlsServiceWS?wsdl';
 * $wsdl[ColissimoAFWsdlClass::WSDL_CACHE_WSDL] = WSDL_CACHE_NONE;
 * $wsdl[ColissimoAFWsdlClass::WSDL_TRACE] = true;
 * $wsdl[ColissimoAFWsdlClass::WSDL_LOGIN] = 'myLogin';
 * $wsdl[ColissimoAFWsdlClass::WSDL_PASSWD] = '**********';
 * etc....
 * Then instantiate the Service class as: 
 * - $wsdlObject = new ColissimoAFWsdlClass($wsdl);
 */
/**
 * Examples
 */


/*************************************
 * Example for ColissimoAFServiceCheck
 */
$colissimoAFServiceCheck = new ColissimoAFServiceCheck();
// sample call for ColissimoAFServiceCheck::checkGenerateLabel()
if($colissimoAFServiceCheck->checkGenerateLabel(new ColissimoAFStructCheckGenerateLabel(/*** update parameters list ***/)))
    print_r($colissimoAFServiceCheck->getResult());
else
    print_r($colissimoAFServiceCheck->getLastError());

/****************************************
 * Example for ColissimoAFServiceGenerate
 */
$colissimoAFServiceGenerate = new ColissimoAFServiceGenerate();
// sample call for ColissimoAFServiceGenerate::generateLabel()
if($colissimoAFServiceGenerate->generateLabel(new ColissimoAFStructGenerateLabel(/*** update parameters list ***/)))
    print_r($colissimoAFServiceGenerate->getResult());
else
    print_r($colissimoAFServiceGenerate->getLastError());
// sample call for ColissimoAFServiceGenerate::generateBordereauByParcelsNumbers()
if($colissimoAFServiceGenerate->generateBordereauByParcelsNumbers(new ColissimoAFStructGenerateBordereauByParcelsNumbers(/*** update parameters list ***/)))
    print_r($colissimoAFServiceGenerate->getResult());
else
    print_r($colissimoAFServiceGenerate->getLastError());

/***********************************
 * Example for ColissimoAFServiceGet
 */
$colissimoAFServiceGet = new ColissimoAFServiceGet();
// sample call for ColissimoAFServiceGet::getBordereauByNumber()
if($colissimoAFServiceGet->getBordereauByNumber(new ColissimoAFStructGetBordereauByNumber(/*** update parameters list ***/)))
    print_r($colissimoAFServiceGet->getResult());
else
    print_r($colissimoAFServiceGet->getLastError());
// sample call for ColissimoAFServiceGet::getListMailBoxPickingDates()
if($colissimoAFServiceGet->getListMailBoxPickingDates(new ColissimoAFStructGetListMailBoxPickingDates(/*** update parameters list ***/)))
    print_r($colissimoAFServiceGet->getResult());
else
    print_r($colissimoAFServiceGet->getLastError());
// sample call for ColissimoAFServiceGet::getProductInter()
if($colissimoAFServiceGet->getProductInter(new ColissimoAFStructGetProductInter(/*** update parameters list ***/)))
    print_r($colissimoAFServiceGet->getResult());
else
    print_r($colissimoAFServiceGet->getLastError());

/************************************
 * Example for ColissimoAFServicePlan
 */
$colissimoAFServicePlan = new ColissimoAFServicePlan();
// sample call for ColissimoAFServicePlan::planPickup()
if($colissimoAFServicePlan->planPickup(new ColissimoAFStructPlanPickup(/*** update parameters list ***/)))
    print_r($colissimoAFServicePlan->getResult());
else
    print_r($colissimoAFServicePlan->getLastError());
