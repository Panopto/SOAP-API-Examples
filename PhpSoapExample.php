<?php

class PanoptoSessionManagementSoapClient extends SoapClient{

        //Namespace used for XML nodes for any root level variables or objects 
        const ROOT_LEVEL_NAMESPACE = "http://tempuri.org/";

        //Namespace used for XML nodes for object members
        const OBJECT_MEMBER_NAMESPACE = "http://schemas.datacontract.org/2004/07/Panopto.Server.Services.PublicAPI.V40";

        //Username of calling user.
        public $ApiUserKey;
        //Auth code generated for calling user.
        public $ApiUserAuthCode;
        //Name of Panopto server being called.
        public $Servername;
        //Password needed if provider does not have a bounce page.
        public $Password;

        // Older PHP SOAP clients fail to pass the SOAPAction header properly.
        // Store the current action so we can insert it in __doRequest.
        public $currentaction;

    public function __construct($servername,$apiuseruserkey, $apiuserauthcode, $password) {
        

        $this->ApiUserKey = $apiuseruserkey;

        $this->ApiUserAuthCode = $apiuserauthcode;

        $this->Servername = $servername;

        $this->Password = $password;

        // Instantiate SoapClient in WSDL mode.
        //Set call timeout to 5 minutes.
        parent::__construct
        (
            "https://". $servername . "/Panopto/PublicAPI/4.6/SessionManagement.svc?wsdl"
        );

    }

      /**
     *  Helper method for making a call to the Panopto API.
     *  $methodname is the case sensitive name of the API method to be called
     *  $namedparams is an associative array of the member parameters (other than authenticationinfo )
     *   required by the API method being called. Keys should be the case sensitive names of the method's 
     *   parameters as specified in the API documentation.
     *  $auth should only be set to false if the method does not require authentication info.
     */
    public function call_web_method($methodname, $namedparams = array(), $auth = true) {
        $params = array();
        
        // Include API user and auth code params unless $auth is set to false.
        if ($auth) 
        {            
            //Create SoapVars for AuthenticationInfo object members    
            $authinfo = new stdClass();


            $authinfo->AuthCode = new SoapVar(
            $this->ApiUserAuthCode, //Data
            XSD_STRING, //Encoding
            null, //type_name should be left null
            null, //type_namespace should be left null
            null, //node_name should be left null                             
            self::OBJECT_MEMBER_NAMESPACE); //Node namespace should be set to proper namespace.

            //Add the password parameter if a password is provided
            if(!empty($this->Password))
            {
                $authinfo->Password = new SoapVar($this->Password, XSD_STRING, null, null, null, self::OBJECT_MEMBER_NAMESPACE);
            }

            $authinfo->AuthCode = new SoapVar($this->ApiUserAuthCode, XSD_STRING, null, null, null, self::OBJECT_MEMBER_NAMESPACE);


            $authinfo->UserKey = new SoapVar($this->ApiUserKey, XSD_STRING, null, null, null,self::OBJECT_MEMBER_NAMESPACE);

            //Create a container for storing all of the soap vars required for the request.
            $obj = array();

            //Add auth info to $obj container
            $obj['auth'] = new SoapVar($authinfo, SOAP_ENC_OBJECT, null, null, null, self::ROOT_LEVEL_NAMESPACE);
           

            //Add the soapvars from namedparams to the container using their key as their member name.
            foreach($namedparams as $key => $value)
            {
                $obj[$key] = $value;
            }

            //Create a soap param using the obj container 
            $param = new SoapParam(new SoapVar($obj, SOAP_ENC_OBJECT), 'data');
            
            //Add the created soap param to an array to be passed to __soapCall
            $params = array($param);
        }

        //Update current action with the method being called.
        $this->currentaction = "http://tempuri.org/ISessionManagement/$methodname";

        // Make the SOAP call via SoapClient::__soapCall.
        return parent::__soapCall($methodname, $params);
    }
    
    /**
     * Sample function for calling an API method. This method will call the sessionmanagement method GetSessionsList.
     * Because this method calls a method from the SessionManagement API, it should only be called by a soap client
     * that has been initialized to SessionManagement.
     * Auth parameter will be created within the soap clients calling logic.
     * $request is a soap encoded ListSessionsRequest object
     * $searchQuery is an optional string containing an custom sql query
     */
    public function get_session_list($request, $searchQuery)
    {
    	$requestvar = new SoapVar($request, SOAP_ENC_OBJECT, null, null, null, self::ROOT_LEVEL_NAMESPACE);
    	$searchQueryVar = new SoapVar($searchQuery, XSD_STRING, null, null, null, self::ROOT_LEVEL_NAMESPACE);
    	
    	return self::call_web_method("GetSessionsList", array("request" => $requestvar, "searchQuery" => $searchQueryVar));
    }
    
    /**
     * Sample function for calling an API method. This method will call the sessionmanagement method GetSessionsById.
     * Because this method calls a method from the SessionManagement API, it should only be called by a soap client
     * that has been initialized to SessionManagement.
     * Auth parameter will be created within the soap clients calling logic.
     * $request is a soap encoded ListSessionsRequest object
     * $searchQuery is an optional string containing an custom sql query
     */
    public function get_sessions_by_id($requestvar)
    {
    	return self::call_web_method("GetSessionsById", $requestvar);
    }
    
      /**
    * Override SOAP action to work around bug in older PHP SOAP versions.
    */
    public function __doRequest($request, $location, $action, $version, $oneway = null) {
        error_log(var_export($request,1));
        return parent::__doRequest($request, $location, $this->currentaction, $version);
    }

}

    //The username of the calling panopto user.
    $UserKey = "blackboardEC2\administrator";

    //The name of the panopto server to make API calls to (i.e. demo.hosted.panopto.com)
    $ServerName = "automation.hosted.panopto.com";

    //The application key from provider on the Panopto provider's page. Should be a string representation of a guid.
    $ApplicationKey = "9441be43-e88e-469b-b3a1-aea5e2b1d0a0";

    //Password of the calling user on Panopto server. Only required if external provider does not have a bounce page.
    $Password = null;

    //generate an auth code
    $AuthCode = generate_auth_code($UserKey, $ServerName, $ApplicationKey);
    

    //Create a SOAP client for the desired Panopto API class, in this cas SessionManagement
    $sessionManagementClient = new PanoptoSessionManagementSoapClient($ServerName, $UserKey, $AuthCode, $Password);
    
    //Set https endpoint in case wsdl specifies http 
    $sessionManagementClient ->__setLocation("https://". $ServerName . "/Panopto/PublicAPI/4.6/SessionManagement.svc");

    
    $requestPagination = Create_Pagination_Object(100, 0);

    //Create a list session request object. Sample values shown here.
    $listSessionsRequest = Create_ListSessionsRequest_Object(
        "2017-05-27T12:12:22",
        "d68ecc7b-08f9-4f4a-b087-f7022b66c378", 
        null, 
        "Name", 
        true, 
        "2009-02-27T12:12:22");

    //Call api and get response
    $response = $sessionManagementClient->get_session_list($listSessionsRequest, "");
   
    //Display response. It will be a json encoded object of type GetSessionsListResult. See API documentation for members.
    var_dump($response);
    
    $listSessionsByIdRequest= Create_ListSessionsByIdRequest_Object(array("085bd329-db16-4084-b911-5a99e0bd53ad", "ae0d10a5-c242-41fd-a5a9-7ab0aaa0da9e"));
    
    $response = $sessionManagementClient->get_sessions_by_id($listSessionsByIdRequest);
    
    //Display response. It will be a json encoded object of type GetSessionsByIdResult. See API documentation for members.
    var_dump($response);

    /*
    *Function to create an api auth code for use when calling methods from the Panopto API.
    */
    function generate_auth_code($userkey, $servername, $applicationkey) {       
        $payload = $userkey . "@" . $servername;
        $signedpayload = $payload . "|" . $applicationkey;
        $authcode = strtoupper(sha1($signedpayload));
        return $authcode;
    }

    
     function Create_Pagination_Object($maxNumberResults, $pageNumber)
     {
         
        //Create empty object to store member data
        $pagination = new stdClass();
        $pagination->MaxNumberResults = $maxNumberResults;
        $pagination->PageNumber = $pageNumber;
        
        return $pagination;
     }
     
     //Example of creating object for use in a SOAP request.
     //This will create a ListSessionsRequest object for use as a parameter in the
     //ISessionManagement.GetSessionsList method.
     //Refer to the API documentation on the requirements and datatypes of members.
     //Members must be created within the containing object in the same order they appear in the documentation.
     //All names are case sensitive.
     function Create_ListSessionsRequest_Object($endDate, $folderId, $remoteRecorderId, $sortBy, $sortIncreasing, $startDate)
     {
     	
     	//Create empty object to store member data
     	$listSessionsRequest = new stdClass();
     	
     	$listSessionsRequest->EndDate = new SoapVar($endDate, XSD_STRING, null, null, null, PanoptoSessionManagementSoapClient::OBJECT_MEMBER_NAMESPACE);
     	$listSessionsRequest->FolderId = new SoapVar($folderId, XSD_STRING, null, null, null, PanoptoSessionManagementSoapClient::OBJECT_MEMBER_NAMESPACE);
     	
     	if ($remoteRecorderId) {
     		$listSessionsRequest->RemoteRecorderId = new SoapVar($remoteRecorderId, XSD_STRING, null, null, null, PanoptoSessionManagementSoapClient::OBJECT_MEMBER_NAMESPACE);
     	}
     	
     	$listSessionsRequest->SortBy = new SoapVar($sortBy, XSD_STRING, null, null, null, PanoptoSessionManagementSoapClient::OBJECT_MEMBER_NAMESPACE);
     	$listSessionsRequest->SortIncreasing = new SoapVar($sortIncreasing, XSD_BOOLEAN, null, null, null, PanoptoSessionManagementSoapClient::OBJECT_MEMBER_NAMESPACE);
     	$listSessionsRequest->StartDate = new SoapVar($startDate, XSD_STRING, null, null, null, PanoptoSessionManagementSoapClient::OBJECT_MEMBER_NAMESPACE);
     	return $listSessionsRequest;
     }
     
     //Example of creating object for use in a SOAP request.
     //This will create a ListSessionsRequest object for use as a parameter in the
     //ISessionManagement.GetSessionsList method.
     //Refer to the API documentation on the requirements and datatypes of members.
     //Members must be created within the containing object in the same order they appear in the documentation.
     //All names are case sensitive.
     function Create_ListSessionsByIdRequest_Object($sessionIdArray)
     {
     	
     	//Create empty object to store member data
     	$listSessionsRequest = new stdClass();
     	$sessionIds = new ArrayObject();
     	foreach($sessionIdArray as $guid) {
     		$sessionIds->append(new SoapVar($guid, XSD_STRING, null, null, null, PanoptoSessionManagementSoapClient::OBJECT_MEMBER_NAMESPACE));
     	}
     	
     	$listSessionsRequest->sessionIds = new SoapVar($sessionIds, SOAP_ENC_ARRAY, null, null, null, PanoptoSessionManagementSoapClient::ROOT_LEVEL_NAMESPACE);
     	
     	return $listSessionsRequest;
     }
?>
