<?php

class SecurityData
{
    /**
     * JWE Key Id.
     *
     * @var string
     */
    public static string $EncryptionKeyId = "7664a2ed0dee4879bdfca0e8ce1ac313";

    /**
     * Access Token.
     *
     * @var string
     */
    public static string $AccessToken = "e6c5e2756e2e41878c852bce8d208632";

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
}