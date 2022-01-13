<?php
/**
 * File for class ColissimoPRServiceFind
 * @package ColissimoPR
 * @subpackage Services
 * @author WsdlToPhp Team <contact@wsdltophp.fr>
 * @version 20150429-01
 * @date 2018-10-26
 */
/**
 * This class stands for ColissimoPRServiceFind originally named Find
 * @package ColissimoPR
 * @subpackage Services
 * @author WsdlToPhp Team <contact@wsdltophp.fr>
 * @version 20150429-01
 * @date 2018-10-26
 */
class ColissimoPRServiceFind extends ColissimoPRWsdlClass
{
    /**
     * Method to call the operation originally named findRDVPointRetraitAcheminementByToken
     * @uses ColissimoPRWsdlClass::getSoapClient()
     * @uses ColissimoPRWsdlClass::setResult()
     * @uses ColissimoPRWsdlClass::saveLastError()
     * @param ColissimoPRStructFindRDVPointRetraitAcheminementByToken $_colissimoPRStructFindRDVPointRetraitAcheminementByToken
     * @return ColissimoPRStructFindRDVPointRetraitAcheminementByTokenResponse
     */
    public function findRDVPointRetraitAcheminementByToken(ColissimoPRStructFindRDVPointRetraitAcheminementByToken $_colissimoPRStructFindRDVPointRetraitAcheminementByToken)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->findRDVPointRetraitAcheminementByToken($_colissimoPRStructFindRDVPointRetraitAcheminementByToken));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named findRDVPointRetraitAcheminement
     * @uses ColissimoPRWsdlClass::getSoapClient()
     * @uses ColissimoPRWsdlClass::setResult()
     * @uses ColissimoPRWsdlClass::saveLastError()
     * @param ColissimoPRStructFindRDVPointRetraitAcheminement $_colissimoPRStructFindRDVPointRetraitAcheminement
     * @return ColissimoPRStructFindRDVPointRetraitAcheminementResponse
     */
    public function findRDVPointRetraitAcheminement(ColissimoPRStructFindRDVPointRetraitAcheminement $_colissimoPRStructFindRDVPointRetraitAcheminement)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->findRDVPointRetraitAcheminement($_colissimoPRStructFindRDVPointRetraitAcheminement));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named findPointRetraitAcheminementByID
     * @uses ColissimoPRWsdlClass::getSoapClient()
     * @uses ColissimoPRWsdlClass::setResult()
     * @uses ColissimoPRWsdlClass::saveLastError()
     * @param ColissimoPRStructFindPointRetraitAcheminementByID $_colissimoPRStructFindPointRetraitAcheminementByID
     * @return ColissimoPRStructFindPointRetraitAcheminementByIDResponse
     */
    public function findPointRetraitAcheminementByID(ColissimoPRStructFindPointRetraitAcheminementByID $_colissimoPRStructFindPointRetraitAcheminementByID)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->findPointRetraitAcheminementByID($_colissimoPRStructFindPointRetraitAcheminementByID));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see ColissimoPRWsdlClass::getResult()
     * @return ColissimoPRStructFindPointRetraitAcheminementByIDResponse|ColissimoPRStructFindRDVPointRetraitAcheminementByTokenResponse|ColissimoPRStructFindRDVPointRetraitAcheminementResponse
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
