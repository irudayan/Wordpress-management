<?php
/**
 * File for class ColissimoPRStructFindRDVPointRetraitAcheminementByTokenResponse
 * @package ColissimoPR
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.fr>
 * @version 20150429-01
 * @date 2018-10-26
 */
/**
 * This class stands for ColissimoPRStructFindRDVPointRetraitAcheminementByTokenResponse originally named findRDVPointRetraitAcheminementByTokenResponse
 * Meta informations extracted from the WSDL
 * - from schema : {@link https://ws.colissimo.fr/pointretrait-ws-cxf/PointRetraitServiceWS/2.0?wsdl}
 * @package ColissimoPR
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.fr>
 * @version 20150429-01
 * @date 2018-10-26
 */
class ColissimoPRStructFindRDVPointRetraitAcheminementByTokenResponse extends ColissimoPRWsdlClass
{
    /**
     * The return
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var ColissimoPRStructRdvPointRetraitAcheminementResult
     */
    public $return;
    /**
     * Constructor method for findRDVPointRetraitAcheminementByTokenResponse
     * @see parent::__construct()
     * @param ColissimoPRStructRdvPointRetraitAcheminementResult $_return
     * @return ColissimoPRStructFindRDVPointRetraitAcheminementByTokenResponse
     */
    public function __construct($_return = NULL)
    {
        parent::__construct(array('return'=>$_return),false);
    }
    /**
     * Get return value
     * @return ColissimoPRStructRdvPointRetraitAcheminementResult|null
     */
    public function getReturn()
    {
        return $this->return;
    }
    /**
     * Set return value
     * @param ColissimoPRStructRdvPointRetraitAcheminementResult $_return the return
     * @return ColissimoPRStructRdvPointRetraitAcheminementResult
     */
    public function setReturn($_return)
    {
        return ($this->return = $_return);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see ColissimoPRWsdlClass::__set_state()
     * @uses ColissimoPRWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return ColissimoPRStructFindRDVPointRetraitAcheminementByTokenResponse
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
