# rest-api-php

A basic REST API implementation with PHP.

## Example

Setup a Rest Service at endpoint `https://localhost/api/main.php`.

```php

include_once("source/loader.php");

///
$service = new Rest\Service();

/// main/123
$service->on("GET", function( array $path ) {
	
	/// TODO
	
	return (object) array( "data" => "output data" );
	
});

///
$service->on("POST", function( array $path, $postData ) {
	
	/// TODO
	
	return (object) array( "data" => "output data" );
	
});

$service->reply();
	
```