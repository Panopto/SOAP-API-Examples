#This example shows how to create a SOAP client using Panopto's wsdl files and the Savon library in Ruby.
#It also provides an example of how to use this client to make calls to the Panopto API, in this case to the
#ListGroups function.

require 'digest/sha1'
require 'uuid'
#This example uses version 2 of the 'Savon' SOAP client library ruby,
#which can be found at http://savonrb.com/version2/
require 'savon'

#    function used to create auth code for SOAP requests
#    'userkey' is either user's Panopto admin's username, or the username decorated with the the external provider's
#     instance name if it is an external admin user.
#    'servername' is the domain name of the Panopto server to make the SOAP request to (e.g. demo.hosted.panopto.com)
#    sharedSecret' is the Application key from the provider on the Panopto Identity Provider's page.
def generate_auth_code(username, servername, shared_secret)
  payload = "#{username}@#{servername}"
  signed_payload = "#{payload}|#{shared_secret}"
  p signed_payload
  (Digest::SHA1.hexdigest signed_payload).upcase
end

# '''
# Admin auth info for making SOAP calls
# '''
#username of admin user in Panopto.
userkey = "adminUser"
#password of admin user on Panopto server. Only required if external provider does not have a bounce page.
password = "password"
#GUID application key from the providers page in Panopto
applicationkey = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
#Name of the panopto server to add the user to.
servername = "demo.panopto.com"


# '''
# Create a SOAP client object using the Savon soap library
# '''
client = Savon.client(
    wsdl: "https://" + servername +"/Panopto/PublicAPI/4.6/UserManagement.svc?wsdl",
    #Must specify endpoint with https, as making calls over ssl is required.
    endpoint: "https://" + servername +"/Panopto/PublicAPI/4.6/UserManagement.svc",
    #Need to disable ssl verification to make SOAP calls over ssl with Savon
    :ssl_verify_mode => :none,
    #We don't want request keys to be converted to any particular casing.
    #The keys should be cased exactly as they appear in the API documentation
    :convert_request_keys_to => :none,
    #Add this namespace (here using the 'pub' identifier), as Savon does not correctly parse it out of the WSDL.
    namespaces: {
        "xmlns:pub" =>  "http://schemas.datacontract.org/2004/07/Panopto.Server.Services.PublicAPI.V40"
    }
)

#
# Generate auth code for making SOAP call using admin user info.
#
authcode = generate_auth_code(userkey, servername, applicationkey)

#
# Create AuthenticationInfo object to be passed to server with SOAP call
#
authentication_info = {
    'pub:AuthCode' => authcode, #All names of object members should have the name of the custom namespace added above appended as a prefix.
    'pub:Password' => password,
    'pub:UserKey' => userkey
}

#Make call to ListGroups using authentication info.
#All object names should have 'tns' appended as a prefix.
client.call(:list_groups, message: {'tns:auth' => authentication_info})




