<?php namespace Auth;

/** Basic
 *	
 *	@ref https://datatracker.ietf.org/doc/html/rfc7617
 *	
 *	O cliente envia as credenciais (normalmente nome de usuário e senha) 
 *	codificadas em base64 no cabeçalho Authorization:
 *	
 *		Authorization: Basic dXNlcm5hbWU6cGFzc3dvcmQ=
 *		Authorization: Basic base64_encode(username:password)
 *	
 *	Atenção! As credenciais são enviadas em texto simples e podem ser interceptadas, 
 *	portanto, é recomendado o uso de HTTPS com Basic Authentication.
 *	
 */
class Basic {
	
	function __construct( string $username, string $password ) {
		
		$this->username = $username;
		$this->password = $password;
		
	}
	
	static function FromHeader() {
		
		$headers = apache_request_headers();
		
		if( !isset( $headers["Authorization"] ) ) 
			return null;
		
		$auth_data = explode(" ", $headers["Authorization"]);
		
		/// verifica se o scheme é do tipo `Basic`
		/// caso não seja, encerra a requisição
		if( $auth_data[0] != "Basic" ) 
			return null;
		
		/// extrai credenciais
		$data = base64_decode( $auth_data[1] );
		$data_array = explode( ":", $data );
		
		return new Basic( $data_array[0], $data_array[1] );
		
	}
	
}

