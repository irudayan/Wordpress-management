<?php
/**
 * File for class ColissimoPRStructRdvPointRetraitAcheminementResult
 * @package ColissimoPR
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.fr>
 * @version 20150429-01
 * @date 2018-10-26
 */
/**
 * This class stands for ColissimoPRStructRdvPointRetraitAcheminementResult originally named rdvPointRetraitAcheminementResult
 * Meta informations extracted from the WSDL
 * - from schema : {@link https://ws.colissimo.fr/pointretrait-ws-cxf/PointRetraitServiceWS/2.0?wsdl}
 * @package ColissimoPR
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.fr>
 * @version 20150429-01
 * @date 2018-10-26
 */
class ColissimoPRStructRdvPointRetraitAcheminementResult extends ColissimoPRStructPointRetraitAcheminementResult
{
    /**
     * The rdv
     * @var boolean
     */
    public $rdv;
    /**
     * Constructor method for rdvPointRetraitAcheminementResult
     * @see parent::__construct()
     * @param boolean $_rdv
     * @return ColissimoPRStructRdvPointRetraitAcheminementResult
     */
    public function __construct($_rdv = NULL)
    {
        ColissimoPRWsdlClass::__construct(array('rdv'=>$_rdv),false);
    }
    /**
     * Get rdv value
     * @return boolean|null
     */
    public function getRdv()
    {
        return $this->rdv;
    }
    /**
     * Set rdv value
     * @param boolean $_rdv the rdv
     * @return boolean
     */
    public function setRdv($_rdv)
    {
        return ($this->rdv = $_rdv);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see ColissimoPRWsdlClass::__set_state()
     * @uses ColissimoPRWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return ColissimoPRStructRdvPointRetraitAcheminementResult
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
