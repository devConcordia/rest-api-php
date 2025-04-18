<?php namespace Auth;

/** Bearer
 *	
 *	@ref https://datatracker.ietf.org/doc/html/rfc6750
 *	
 *	O token é então enviado no cabeçalho Authorization como Bearer <token>
 *	
 *	Esse método é mais seguro que o Basic Authentication, pois não 
 *	carregam informações confidenciais diretamente.
 *	
 */
class Bearer {
	
	function __construct() {
		
		$headers = apache_request_headers();
		
		if( !isset( $headers["Authorization"] ) ) 
			\Rest\Response::Unauthorized("authorization-invalid");
		
		$auth_data = explode(" ", $headers["Authorization"]);
		
		/// verifica se o scheme é do tipo `Bearer`
		/// caso não seja, encerra a requisição
		if( strtolower($auth_data[0]) != "bearer" )
			\Rest\Response::Unauthorized("authorization-invalid");
		
		/// para a plataforma VirtualID, o JSONWebToken 
		/// deve estar codificado em base64
		$this->token = base64_decode( $auth_data[1] );
		
	}
	
}
