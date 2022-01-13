<?php
/**
 * File for class ColissimoAFStructGenerateBordereauByParcelsNumbers
 * @package ColissimoAF
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2018-10-26
 */
/**
 * This class stands for ColissimoAFStructGenerateBordereauByParcelsNumbers originally named generateBordereauByParcelsNumbers
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://ws.Colissimo.fr/sls-ws/SlsServiceWS?wsdl}
 * @package ColissimoAF
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2018-10-26
 */
class ColissimoAFStructGenerateBordereauByParcelsNumbers extends ColissimoAFWsdlClass
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
     * The generateBordereauParcelNumberList
     * @var ColissimoAFStructGenerateBordereauParcelNumberList
     */
    public $generateBordereauParcelNumberList;
    /**
     * Constructor method for generateBordereauByParcelsNumbers
     * @see parent::__construct()
     * @param string $_contractNumber
     * @param string $_password
     * @param ColissimoAFStructGenerateBordereauParcelNumberList $_generateBordereauParcelNumberList
     * @return ColissimoAFStructGenerateBordereauByParcelsNumbers
     */
    public function __construct($_contractNumber = NULL,$_password = NULL,$_generateBordereauParcelNumberList = NULL)
    {
        parent::__construct(array('contractNumber'=>$_contractNumber,'password'=>$_password,'generateBordereauParcelNumberList'=>$_generateBordereauParcelNumberList),false);
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
     * Get generateBordereauParcelNumberList value
     * @return ColissimoAFStructGenerateBordereauParcelNumberList|null
     */
    public function getGenerateBordereauParcelNumberList()
    {
        return $this->generateBordereauParcelNumberList;
    }
    /**
     * Set generateBordereauParcelNumberList value
     * @param ColissimoAFStructGenerateBordereauParcelNumberList $_generateBordereauParcelNumberList the generateBordereauParcelNumberList
     * @return ColissimoAFStructGenerateBordereauParcelNumberList
     */
    public function setGenerateBordereauParcelNumberList($_generateBordereauParcelNumberList)
    {
        return ($this->generateBordereauParcelNumberList = $_generateBordereauParcelNumberList);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see ColissimoAFWsdlClass::__set_state()
     * @uses ColissimoAFWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return ColissimoAFStructGenerateBordereauByParcelsNumbers
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
