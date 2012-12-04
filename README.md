HTTP Client
===========

Provides a basic HTTP client that wraps the PHP curl extension.

Making a GET request:

```
$client = new Net_Http_Client();
$client->get("http://bigcommerce.com/");
$body = $client->getBody();
$contentType = $client->getHeader("Content-Type");
```

Making a POST request:

```
$client = new Net_Http_Client();
$client->post("http://bigcommerce.com/contact.php", array("key"=>"value"));
$responseCode = $client->getStatus();
if ($responseCode != 200) {
    // the request returned an error response
}
```