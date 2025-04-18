<?php namespace Auth;

/** base64url_encode
 *	
 *		Replaces + by - (minus)
 *		Replaces / by _ (underline)
 *		Does not require a padding character Forbids line separators
 *	
 *	@param {String} input
 *	@return {String}
 */
function base64url_encode( string $input ) {
	
	$output = base64_encode( $input );
	
	$output = str_replace("+", "-", $output);
	$output = str_replace("/", "_", $output);
	
	return output;
	
}

/** base64url_decode
 *	
 *		Replaces + by - (minus)
 *		Replaces / by _ (underline)
 *		Does not require a padding character Forbids line separators
 *	
 *	@param {String} input
 *	@return {String}
 */
function base64url_decode( $input ) {
	
	$input = str_replace("-", "+", $input);
	$input = str_replace("_", "/", $input);
	
	return base64_decode( $input );
	
}


/** JSONWebToken
 *	
 *	@ref https://jwt.io/
 *	
 *	O token é obtido do header Authorization como Bearer <token>
 *	
 *		Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c
 *		Authorization: Bearer HEAD.PAYLOAD.SIGNATURE
 *	
 *	HEAD = base64url_encode({
 *		{string} alg,			Cryptography Algorithm	(simetrico: HS256, HS384, HS512; assimetrico: RS256, RS384 e RS512)
 *		{string} kid			Key ID
 *	})
 *	
 *	PAYLOAD = base64url_encode({					
 *	
 *	})
 *	
 *	SIGNATURE = base64url_encode(binary)
 *	
 */
class JSONWebToken extends Bearer {
	
	function __construct() {
		
		parent::__construct();
		
		///
		$token = explode( ".", $this->data );
		
		///
		$this->token = $token;
		
		$this->head = json_decode( base64url_decode( $token[0] ) );
		$this->payload = json_decode( base64url_decode( $token[1] ) );
		$this->signature = $token[2];
		
	}
	
	/**	verifySign
	 *	
	 *	@param {string|OpenSSLAsymmetricKey} $key
	 *	@param {string} $alg							HS256 | HS384 | HS512 | RS256 | RS384 | RS512
	 *	@return {bool}
	 */
	public function verifySign( $key, string $alg = "" ) {
		
		$sign = base64url_decode( $this->signature );
		
		$data = $this->token[0] .".". $this->token[1];
		
		/// 
		if( empty($alg) ) 
			$alg = $this->head->alg;
		
		///
		if( substr( $alg, 0, 2 ) == "RS" ) {
			if( gettype($key) == "string" ) {
				
			//	if( !str_starts_with( $key, "-----BEGIN PUBLIC KEY-----" ) ) {
			//		$key = implode( "\n", str_split( $key, 64 ));
			//		$key = "-----BEGIN PUBLIC KEY-----\n". $key ."\n-----END PUBLIC KEY-----";
			//	}
				
				$key = openssl_pkey_get_public( $key );
		
			}
		}
		
		///
		switch( $alg ) {
			
			case 'HS256':
			case 'HS384':
			case 'HS512':
				$type = "sha". substr( $alg, 2 );
				return hash_hmac( $type, $data, $key, true ) == $sign;
				break;
			
			case 'RS256':
				return openssl_verify( $data, $sign, $key, OPENSSL_ALGO_SHA256 );
				break;
				
			case 'RS384':
				return openssl_verify( $data, $sign, $key, OPENSSL_ALGO_SHA384 );
				break;
				
			case 'RS512':
				return openssl_verify( $data, $sign, $key, OPENSSL_ALGO_SHA512 );
				break;
			
		}
		
		return false;
	
	}
	
}

