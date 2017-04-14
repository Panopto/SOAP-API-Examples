<?php
/**
 * File for class SessionManagementStructAuthenticationInfo
 * @package SessionManagement
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * This class stands for SessionManagementStructAuthenticationInfo originally named AuthenticationInfo
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://scratch.beta.panopto.com/Panopto/PublicAPI/4.6/SessionManagement.svc?xsd=xsd2}
 * @package SessionManagement
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2017-01-19
 */
class SessionManagementStructAuthenticationInfo extends SessionManagementWsdlClass
{
    /**
     * The AuthCode
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $AuthCode;
    /**
     * The Password
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Password;
    /**
     * The UserKey
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $UserKey;
    /**
     * Constructor method for AuthenticationInfo
     * @see parent::__construct()
     * @param string $_authCode
     * @param string $_password
     * @param string $_userKey
     * @return SessionManagementStructAuthenticationInfo
     */
    public function __construct($_authCode = NULL,$_password = NULL,$_userKey = NULL)
    {
        parent::__construct(array('AuthCode'=>$_authCode,'Password'=>$_password,'UserKey'=>$_userKey),false);
    }
    /**
     * Get AuthCode value
     * @return string|null
     */
    public function getAuthCode()
    {
        return $this->AuthCode;
    }
    /**
     * Set AuthCode value
     * @param string $_authCode the AuthCode
     * @return string
     */
    public function setAuthCode($_authCode)
    {
        return ($this->AuthCode = $_authCode);
    }
    /**
     * Get Password value
     * @return string|null
     */
    public function getPassword()
    {
        return $this->Password;
    }
    /**
     * Set Password value
     * @param string $_password the Password
     * @return string
     */
    public function setPassword($_password)
    {
        return ($this->Password = $_password);
    }
    /**
     * Get UserKey value
     * @return string|null
     */
    public function getUserKey()
    {
        return $this->UserKey;
    }
    /**
     * Set UserKey value
     * @param string $_userKey the UserKey
     * @return string
     */
    public function setUserKey($_userKey)
    {
        return ($this->UserKey = $_userKey);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see SessionManagementWsdlClass::__set_state()
     * @uses SessionManagementWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return SessionManagementStructAuthenticationInfo
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
