vkClient
========

Serverside, userless vk.com api client

Use all vk.com api without user interaction!
All you need is create vk application and provide vk user requisites.

## Usage

* Create standalone app (http://vk.com/editapp?act=create)
* Specify "Site address" and "Base domain" in application settings.  
    This domain must be visible from server with VkClient running on. So, you can choose domain name even "localhost".
* Create dummy file and place it on "Base domain".
* Create VkClient instance:

	```php
	$vk = new VkClient($applicationId, $email, $password, $scope, $linkToDummyFile);
	```
	* `$applicationId` - application id
	* `$email`, `$password` - requisites of vk user. Script will login at vk.com with this requisites and make api requests from this user.
	* `$scope` - access rights requested by your app (http://vk.com/developers.php?oid=-17680044&p=Application_Rights)
	* `$linkToDummyFile` - URL to dummy file. Used for redirect with access_token from vk servers.
* Use it:

	```php
	$response = $this->makeApiCall('friends.get', array(
		'uid' => $uid
	));
	
	echo "friends count = ".count($response);
	```