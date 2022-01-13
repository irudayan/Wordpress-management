<?php
/**
 * File for class ColissimoAFServiceCheck
 * @package ColissimoAF
 * @subpackage Services
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2018-10-26
 */
/**
 * This class stands for ColissimoAFServiceCheck originally named Check
 * @package ColissimoAF
 * @subpackage Services
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2018-10-26
 */
class ColissimoAFServiceCheck extends ColissimoAFWsdlClass
{
    /**
     * Method to call the operation originally named checkGenerateLabel
     * @uses ColissimoAFWsdlClass::getSoapClient()
     * @uses ColissimoAFWsdlClass::setResult()
     * @uses ColissimoAFWsdlClass::saveLastError()
     * @param ColissimoAFStructCheckGenerateLabel $_colissimoAFStructCheckGenerateLabel
     * @return ColissimoAFStructCheckGenerateLabelResponse
     */
    public function checkGenerateLabel(ColissimoAFStructCheckGenerateLabel $_colissimoAFStructCheckGenerateLabel)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->checkGenerateLabel($_colissimoAFStructCheckGenerateLabel));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see ColissimoAFWsdlClass::getResult()
     * @return ColissimoAFStructCheckGenerateLabelResponse
     */
    public function getResult()
    {
        return parent::getResult();
    }
    /**
     * Method returning the class name
     * @return string __CLASS__
     */
    public function __toString()
    {
        return __CLASS__;
    }
}
