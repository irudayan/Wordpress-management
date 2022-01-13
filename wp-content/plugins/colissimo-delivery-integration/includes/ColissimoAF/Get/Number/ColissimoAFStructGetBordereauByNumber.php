<?php
/**
 * File for class ColissimoAFStructGetBordereauByNumber
 * @package ColissimoAF
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2018-10-26
 */
/**
 * This class stands for ColissimoAFStructGetBordereauByNumber originally named getBordereauByNumber
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://ws.Colissimo.fr/sls-ws/SlsServiceWS?wsdl}
 * @package ColissimoAF
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2018-10-26
 */
class ColissimoAFStructGetBordereauByNumber extends ColissimoAFWsdlClass
{
    /**
     * The contractNumber
     * @var string
     */
    public $contractNumber;
    /**
     * The password
     * @var string
     */
    public $password;
    /**
     * The bordereauNumber
     * @var string
     */
    public $bordereauNumber;
    /**
     * Constructor method for getBordereauByNumber
     * @see parent::__construct()
     * @param string $_contractNumber
     * @param string $_password
     * @param string $_bordereauNumber
     * @return ColissimoAFStructGetBordereauByNumber
     */
    public function __construct($_contractNumber = NULL,$_password = NULL,$_bordereauNumber = NULL)
    {
        parent::__construct(array('contractNumber'=>$_contractNumber,'password'=>$_password,'bordereauNumber'=>$_bordereauNumber),false);
    }
    /**
     * Get contractNumber value
     * @return string|null
     */
    public function getContractNumber()
    {
        return $this->contractNumber;
    }
    /**
     * Set contractNumber value
     * @param string $_contractNumber the contractNumber
     * @return string
     */
    public function setContractNumber($_contractNumber)
    {
        return ($this->contractNumber = $_contractNumber);
    }
    /**
     * Get password value
     * @return string|null
     */
    public function getPassword()
    {
        return $this->password;
    }
    /**
     * Set password value
     * @param string $_password the password
     * @return string
     */
    public function setPassword($_password)
    {
        return ($this->password = $_password);
    }
    /**
     * Get bordereauNumber value
     * @return string|null
     */
    public function getBordereauNumber()
    {
        return $this->bordereauNumber;
    }
    /**
     * Set bordereauNumber value
     * @param string $_bordereauNumber the bordereauNumber
     * @return string
     */
    public function setBordereauNumber($_bordereauNumber)
    {
        return ($this->bordereauNumber = $_bordereauNumber);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see ColissimoAFWsdlClass::__set_state()
     * @uses ColissimoAFWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return ColissimoAFStructGetBordereauByNumber
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
