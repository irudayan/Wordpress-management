<?php
/**
 * File for the class which returns the class map definition
 * @package ColissimoPR
 * @author WsdlToPhp Team <contact@wsdltophp.fr>
 * @version 20150429-01
 * @date 2018-10-26
 */
/**
 * Class which returns the class map definition by the static method ColissimoPRClassMap::classMap()
 * @package ColissimoPR
 * @author WsdlToPhp Team <contact@wsdltophp.fr>
 * @version 20150429-01
 * @date 2018-10-26
 */
class ColissimoPRClassMap
{
    /**
     * This method returns the array containing the mapping between WSDL structs and generated classes
     * This array is sent to the SoapClient when calling the WS
     * @return array
     */
    final public static function classMap()
    {
        return array (
  'Conges' => 'ColissimoPRStructConges',
  'Exception' => 'ColissimoPRStructException',
  'PointRetrait' => 'ColissimoPRStructPointRetrait',
  'findPointRetraitAcheminementByID' => 'ColissimoPRStructFindPointRetraitAcheminementByID',
  'findPointRetraitAcheminementByIDResponse' => 'ColissimoPRStructFindPointRetraitAcheminementByIDResponse',
  'findRDVPointRetraitAcheminement' => 'ColissimoPRStructFindRDVPointRetraitAcheminement',
  'findRDVPointRetraitAcheminementByToken' => 'ColissimoPRStructFindRDVPointRetraitAcheminementByToken',
  'findRDVPointRetraitAcheminementByTokenResponse' => 'ColissimoPRStructFindRDVPointRetraitAcheminementByTokenResponse',
  'findRDVPointRetraitAcheminementResponse' => 'ColissimoPRStructFindRDVPointRetraitAcheminementResponse',
  'pointRetraitAcheminement' => 'ColissimoPRStructPointRetraitAcheminement',
  'pointRetraitAcheminementByIDResult' => 'ColissimoPRStructPointRetraitAcheminementByIDResult',
  'pointRetraitAcheminementResult' => 'ColissimoPRStructPointRetraitAcheminementResult',
  'rdvPointRetraitAcheminementResult' => 'ColissimoPRStructRdvPointRetraitAcheminementResult',
);
    }
}
