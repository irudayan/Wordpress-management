<?php
/**
 * File for class ColissimoAFServiceGenerate
 * @package ColissimoAF
 * @subpackage Services
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2018-10-26
 */
/**
 * This class stands for ColissimoAFServiceGenerate originally named Generate
 * @package ColissimoAF
 * @subpackage Services
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2018-10-26
 */
class ColissimoAFServiceGenerate extends ColissimoAFWsdlClass
{
    /**
     * Method to call the operation originally named generateLabel
     * @uses ColissimoAFWsdlClass::getSoapClient()
     * @uses ColissimoAFWsdlClass::setResult()
     * @uses ColissimoAFWsdlClass::saveLastError()
     * @param ColissimoAFStructGenerateLabel $_colissimoAFStructGenerateLabel
     * @return ColissimoAFStructGenerateLabelResponse
     */
    public function generateLabel(ColissimoAFStructGenerateLabel $_colissimoAFStructGenerateLabel)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->generateLabel($_colissimoAFStructGenerateLabel));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named generateBordereauByParcelsNumbers
     * @uses ColissimoAFWsdlClass::getSoapClient()
     * @uses ColissimoAFWsdlClass::setResult()
     * @uses ColissimoAFWsdlClass::saveLastError()
     * @param ColissimoAFStructGenerateBordereauByParcelsNumbers $_colissimoAFStructGenerateBordereauByParcelsNumbers
     * @return ColissimoAFStructGenerateBordereauByParcelsNumbersResponse
     */
    public function generateBordereauByParcelsNumbers(ColissimoAFStructGenerateBordereauByParcelsNumbers $_colissimoAFStructGenerateBordereauByParcelsNumbers)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->generateBordereauByParcelsNumbers($_colissimoAFStructGenerateBordereauByParcelsNumbers));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see ColissimoAFWsdlClass::getResult()
     * @return ColissimoAFStructGenerateBordereauByParcelsNumbersResponse|ColissimoAFStructGenerateLabelResponse
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
