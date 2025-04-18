<?php namespace Rest;

/** Response
 *	
 *	@ref https://developer.mozilla.org/en-US/docs/Web/HTTP/Status
 *	
 */
class Response {
	
	/// some status codes
	/// @ref https://developer.mozilla.org/en-US/docs/Web/HTTP/Status
	const OK = 200;
	const CREATED = 201;
	const BAD_REQUEST = 400;
	const UNAUTHORIZED = 401;
	const INTERNAL_SERVER_ERROR = 500;
	
	/// @ref https://developer.mozilla.org/en-US/docs/Web/HTTP/MIME_types
	const JSON = "application/json";
	const XML = "application/xml";
	const TEXT = "text/plain";
	const HTML = "text/html";
	
	/** Reply
	 *	
	 */
	static function Reply( string $data, string $mimeType, int $statusCode ) {
		
		http_response_code( $statusCode );
		header( "Content-Type: ". $mimeType );
		
		exit( $data );
		
	}
	
	/**	Json
	 * 	
	 * 	This method closes the request.
	 * 	
	 * 	@param {object|array|string} $data
	 */
	static function Json( $data, int $statusCode = Response::OK ) {
		
		if( gettype( $data ) != "string" ) 
			$data = json_encode( $data, JSON_UNESCAPED_SLASHES );
		
		///
		Response::Reply( $data, Response::JSON, $statusCode );
		
	}
	
	/** Error
	 *	
	 */
	static function Error( string $message, int $statusCode = Response::BAD_REQUEST ) {
		
		///
		Response::Json( array( "error" => $message ), $statusCode );
		
	}
	
	/**	Unauthorized
	 * 	
	 */
	static function Unauthorized( string $message = "unauthorized" ) {
		
		header('WWW-Authenticate: Bearer realm="Auth"');
		//header('HTTP/1.0 401 Unauthorized');
		
		///
		Response::Error( $message, Response::UNAUTHORIZED );
		
	}
	
	/** ReplyOnError
	 *	
	 *	Listen throws exceptions and close when an error occurs.
	 *	If the exception code is 0, the message will contain details of error (script and line).
	 *	
	 */
	static function ReplyOnError() {
		
		/// disable error messages
		error_reporting(0);
		
		ini_set("display_errors", 0);
		
		/// fatal error
		register_shutdown_function(function() {
			
			$error = error_get_last();
			
			if( $error['type'] === E_ERROR )
				Response::Error( "internal-server-error", Response::INTERNAL_SERVER_ERROR );
			
		});
		
		///
		set_exception_handler(function( $e ) {
			
			if( $e->getCode() == 0 ) {
				
				$message = $e->getMessage() ."\n". $e->getFile() ."[". $e->getLine() ."]";
				
				Response::Error( $message, Response::INTERNAL_SERVER_ERROR );
				
			} else {
				
				Response::Error( $e->getMessage(), Response::INTERNAL_SERVER_ERROR );
				
			}
			
		});
		
	}
	
}
