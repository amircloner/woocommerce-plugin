<?php

use Firebase\JWT\JWT;

/**
 * Class Generace_App_Control_Public_Key
 * @author ngocdt@rnlab.io
 * @since 1.2.3
 */
class Generace_App_Control_Public_Key {

	public static function getPublicKey() {
		$publicKeys        = file_get_contents( 'https://appleid.apple.com/auth/keys' );
		$decodedPublicKeys = json_decode( $publicKeys, true );

		if ( ! isset( $decodedPublicKeys['keys'] ) || count( $decodedPublicKeys['keys'] ) < 1 ) {
			throw new Exception( 'Invalid key format.' );
		}

		$parsedKeyData    = $decodedPublicKeys['keys'][0];
		$parsedPublicKey  = self::parseKey( $parsedKeyData );
		$publicKeyDetails = openssl_pkey_get_details( $parsedPublicKey );

		if ( ! isset( $publicKeyDetails['key'] ) ) {
			throw new Exception( 'Invalid public key details.' );
		}

		return [
			'publicKey' => $publicKeyDetails['key'],
			'alg'       => $parsedKeyData['alg']
		];

	}

	public static function parseKey( $source ) {
		if ( ! is_array( $source ) ) {
			$source = (array) $source;
		}
		if ( ! empty( $source ) && isset( $source['kty'] ) && isset( $source['n'] ) && isset( $source['e'] ) ) {
			switch ( $source['kty'] ) {
				case 'RSA':
					if ( array_key_exists( 'd', $source ) ) {
						throw new UnexpectedValueException( 'Failed to parse JWK: RSA private key is not supported' );
					}
					$pem  = self::toPEM( $source['n'], $source['e'] );
					$pKey = openssl_pkey_get_public( $pem );
					if ( $pKey !== false ) {
						return $pKey;
					}
					break;
				default:
					//Currently only RSA is supported
					break;
			}
		}
		throw new Exception( 'Failed to parse JWK' );
	}

	private static function toPEM( $n, $e ) {
		$modulus        = JWT::urlsafeB64Decode( $n );
		$publicExponent = JWT::urlsafeB64Decode( $e );
		$components     = array(
			'modulus'        => pack( 'Ca*a*', 2, self::encodeLength( strlen( $modulus ) ), $modulus ),
			'publicExponent' => pack( 'Ca*a*', 2, self::encodeLength( strlen( $publicExponent ) ), $publicExponent )
		);
		$RSAPublicKey   = pack(
			'Ca*a*a*',
			48,
			self::encodeLength( strlen( $components['modulus'] ) + strlen( $components['publicExponent'] ) ),
			$components['modulus'],
			$components['publicExponent']
		);
		// sequence(oid(1.2.840.113549.1.1.1), null)) = rsaEncryption.
		$rsaOID       = pack( 'H*', '300d06092a864886f70d0101010500' ); // hex version of MA0GCSqGSIb3DQEBAQUA
		$RSAPublicKey = chr( 0 ) . $RSAPublicKey;
		$RSAPublicKey = chr( 3 ) . self::encodeLength( strlen( $RSAPublicKey ) ) . $RSAPublicKey;
		$RSAPublicKey = pack(
			'Ca*a*',
			48,
			self::encodeLength( strlen( $rsaOID . $RSAPublicKey ) ),
			$rsaOID . $RSAPublicKey
		);
		$RSAPublicKey = "-----BEGIN PUBLIC KEY-----\r\n" .
		                chunk_split( base64_encode( $RSAPublicKey ), 64 ) .
		                '-----END PUBLIC KEY-----';

		return $RSAPublicKey;
	}

	private static function encodeLength( $length ) {
		if ( $length <= 0x7F ) {
			return chr( $length );
		}
		$temp = ltrim( pack( 'N', $length ), chr( 0 ) );

		return pack( 'Ca*', 0x80 | strlen( $temp ), $temp );
	}
}