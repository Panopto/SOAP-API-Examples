#This example shows how to create a SOAP client using Panopto's wsdl files and the pysimplesoap library in Python. 
#It also shows how to construct an object to use as an argument in these calls, and provides examples of calling 
#both the CreateUser and AddMembersToInternalGroup methods from the API.

import hashlib
import uuid
#This demonstration uses the pysimplesoap (soap2py) library.
#It may be downloaded from https://code.google.com/p/pysimplesoap/
from pysimplesoap.client import SoapClient

'''
    function used to create auth code for SOAP requests
    'userkey' is either user's Panopto admin's username, or the username decorated with the the external provider's instance name if it is an external admin user.
    'servername' is the domain name of the Panopto server to make the SOAP request to (e.g. demo.hosted.panopto.com)
    'sharedSecret' is the Application key from the provider on the Panopto Identity Provider's page.
'''
def generateauthcode(userkey, servername, sharedSecret):
    payload = userkey + '@' + servername
    signedPayload = payload + '|' + sharedSecret
    m = hashlib.sha1()
    m.update(signedPayload)
    authcode = m.hexdigest().upper()
    return authcode

'''
Create a new public ID for the user to be created.
'''
studentUserID = uuid.uuid1()

'''
Create a user object with information for the new user. Parameters here must be in this order to be accepted by the server
'''
studentUser = {'Email': "test@test.com",
               'EmailSessionNotifications': "true",
               'FirstName': "Student",
               #Optional list of GUIDs as strings of the groups that the user should be a member in.
               'GroupMemberships': ["xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"],
               'LastName': "User",
               #User's role on Panotpo. "None" will grant the user the sitewide 'Viewer' role.
               'SystemRole': "None",
                #Optional description of user.
               'UserBio': "",
               'UserId': str(studentUserID),
               #Panopto login ID for student. May be anything.
               'UserKey': "studentuser",
               #Optional custom URL for user's settings.
               'UserSettingsUrl': ""
               }

'''
Admin auth info for making SOAP calls
'''
#username of admin user in Panopto.
userkey = "admin"
#password of admin user on Panopto server. Only required if external provider does not have a bounce page.
password = "password"
 #Instance name of external provider on Panotpo
providername = "MyProvider"
#Application key from provider on Panotpo
applicationkey = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
#Name of the panopto server to add the user to.
servername = "demo.panopto.com"


'''
Create a SOAP client object using the
'''
client = SoapClient(wsdl="https://" + servername +"/Panopto/PublicAPI/4.6/UserManagement.svc?wsdl", trace=False)

'''
Generate auth code for making SOAP call using admin user info.
'''
authcode = generateauthcode(userkey, servername, applicationkey)

'''
 Create AuthenticationInfo object to be passed to server with SOAP call
'''
AuthenticationInfo = {'AuthCode':authcode, 'Password':password, 'UserKey':userkey}

'''
Soap call to create user in panopto. Result will contain user's public ID if successful.
'''
createUserResponse = client.CreateUser(
    auth = AuthenticationInfo,
    user = studentUser,
    #Initial password for created user. This may be reset manually.
    initialPassword = "studentpassword"
)
#Show response from attempt to create user.
print createUserResponse

'''
Soap call to add a user to an internal group in Panopto.
This will give them the permissions inherited by members
of the group on associated folders and sessions.
'''
addMembersToGroupResponse = client.AddMembersToInternalGroup(
    auth = AuthenticationInfo,
    #public GUID of group to add members to
    groupId = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx",
    #Initial password for created user. This may be reset manually.
    memberIDs = ["yyyyyyyy-yyyy-yyyy-yyyy-yyyyyyyyyyyy", "zzzzzzzz-zzzz-zzzz-zzzz-zzzzzzzzzzzz"]
)

#Show response from attempt to add members to group.
print addMembersToGroupResponse


