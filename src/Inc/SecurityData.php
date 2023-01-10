<?php
/**
 * Security Data. Some of the required tokens etc.
 * 
 * @since 2.0.4
 */

class SecurityData
{
    /**
     * JWE Key Id.
     *
     * @var string
     */
    public static string $EncryptionKeyId = "19f84b5655f04e25a99b09f1ee2fac78";

    /**
     * Token Type - Used in JWS and JWE header.
     *
     * @var string
     */
    public static string $TokenType = "JWT";

    /**
     * JWS (JSON Web Signature) Signature Algorithm - This parameter identifies the cryptographic algorithm used to
     * secure the JWS.
     *
     * @var string
     */
    public static string $JWSAlgorithm = "PS256";

    /**
     * JWE (JSON Web Encryption) Key Encryption Algorithm - This parameter identifies the cryptographic algorithm
     * used to secure the JWE.
     *
     * @var string
     */
    public static string $JWEAlgorithm = "RSA-OAEP";

    /**
     * JWE (JSON Web Encryption) Content Encryption Algorithm - This parameter identifies the content encryption
     * algorithm used on the plaintext to produce the encrypted ciphertext.
     *
     * @var string
     */
    public static string $JWEEncrptionAlgorithm = "A128CBC-HS256";

    /**
     * Get access token.
     *
     * @since 2.0.7
     */
    public static function accessToken() {
        return get_option( 'woocommerce_hbl-payment_settings' )['merchant_password'] ? get_option( 'woocommerce_hbl-payment_settings' )['merchant_password'] : '';
    }
}