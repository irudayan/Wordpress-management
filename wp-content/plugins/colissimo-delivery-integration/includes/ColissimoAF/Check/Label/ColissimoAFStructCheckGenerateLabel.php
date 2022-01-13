<?php
/**
 * File for class ColissimoAFStructCheckGenerateLabel
 * @package ColissimoAF
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2018-10-26
 */
/**
 * This class stands for ColissimoAFStructCheckGenerateLabel originally named checkGenerateLabel
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://ws.Colissimo.fr/sls-ws/SlsServiceWS?wsdl}
 * @package ColissimoAF
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2018-10-26
 */
class ColissimoAFStructCheckGenerateLabel extends ColissimoAFWsdlClass
{
    /**
     * The checkGenerateLabelRequest
     * @var ColissimoAFStructCheckGenerateLabelRequest
     */
    public $checkGenerateLabelRequest;
    /**
     * Constructor method for checkGenerateLabel
     * @see parent::__construct()
     * @param ColissimoAFStructCheckGenerateLabelRequest $_checkGenerateLabelRequest
     * @return ColissimoAFStructCheckGenerateLabel
     */
    public function __construct($_checkGenerateLabelRequest = NULL)
    {
        parent::__construct(array('checkGenerateLabelRequest'=>$_checkGenerateLabelRequest),false);
    }
    /**
     * Get checkGenerateLabelRequest value
     * @return ColissimoAFStructCheckGenerateLabelRequest|null
     */
    public function getCheckGenerateLabelRequest()
    {
        return $this->checkGenerateLabelRequest;
    }
    /**
     * Set checkGenerateLabelRequest value
     * @param ColissimoAFStructCheckGenerateLabelRequest $_checkGenerateLabelRequest the checkGenerateLabelRequest
     * @return ColissimoAFStructCheckGenerateLabelRequest
     */
    public function setCheckGenerateLabelRequest($_checkGenerateLabelRequest)
    {
        return ($this->checkGenerateLabelRequest = $_checkGenerateLabelRequest);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see ColissimoAFWsdlClass::__set_state()
     * @uses ColissimoAFWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return ColissimoAFStructCheckGenerateLabel
     */
    public static function __set_state(array $_array,$_className = __CLASS__)
    {
        return parent::__set_state($_array,$_className);
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
