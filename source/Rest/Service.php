<?php namespace Rest;

/** Service
 *	
 *	
 *		URI = scheme ":" hier-part [ "?" query ] [ "#" fragment ]
 *	
 *		   foo://example.com:8042/over/there?name=ferret#nose
 *	       \_/   \______________/\_________/ \_________/ \__/
 *	        |           |            |            |        |
 *	     scheme     authority       path        query   fragment
 *	        |   _____________________|__
 *	       / \ /                        \
 *	       urn:example:animal:ferret:nose
 *	
 *	@ref https://www.rfc-editor.org/rfc/rfc9205.html#name-using-http-methods
 *		4.6. Using HTTP Status Codes 	
 *	
 */
class Service {
	
	private $handlers = array();
	
	public function on( string $method, $handler ) {
		
		$this->handlers[ $method ] = $handler;
		
	}
	
	public function reply() {
		
        $requestMethod = $_SERVER['REQUEST_METHOD'];
		
		if( isset( $this->handlers[ $requestMethod ] ) ) {
			
			$handler = $this->handlers[ $requestMethod ];
			
			///
			$uri_base = dirname( $_SERVER["PHP_SELF"] );
			$uri_path = str_replace( $uri_base, '', $_SERVER['REQUEST_URI'] );
			$uri_path = explode( "?", $uri_path );
			
			$path = array_values( array_diff( explode( "/", $uri_path[0] ), array("") ) );
			
			///
		//	$search = $_GET;
			
			///
			$data = file_get_contents( "php://input" );
			$data = json_decode( $data );
			
			try {
			
				///
			//	$output = call_user_func_array( $handler, array( $path, $search, $data ));
				$output = call_user_func_array( $handler, array( $path, $data ));
				
				///
				Response::Json( $output, 200 );
			
			} catch( Exception $e ) {
			
				Response::Error( $e->getMessage(), 501 );			
				
			}
			
		}
		
	}
	
}
