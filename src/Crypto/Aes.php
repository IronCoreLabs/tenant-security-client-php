<?php

declare(strict_types=1);

namespace IronCore\Crypto;

use IronCore\Bytes;
use IronCore\DocumentParts;
use IronCore\Exception\CryptoException;
use IronCore\V3HeaderSignature;
use Proto\IronCoreLabs\SaaSShieldHeader;
use Proto\IronCoreLabs\V3DocumentHeader;

/**
 * Cryptographic functions. Not intended to be used by consumers of the SDK.
 */
trait Aes
{
    /**
     * Gets the current IronCore document header version as a single byte.
     *
     * @return Bytes Byte representation of IronCore document header version
     */
    private static function getCurrentDocumentHeaderVersion(): Bytes
    {
        return new Bytes(pack("C", 3));
    }

    /**
     * Gets IronCore document magic bytes that are included in every document's preamble
     *
     * @return Bytes IronCore document magic bytes
     */
    private static function getDocumentMagic(): Bytes
    {
        return new Bytes("IRON");
    }

    /**
     * Encrypts given plaintext with the provided key.
     *
     * @param Bytes $plaintext Bytes to encrypt
     * @param Bytes $key 32-byte secret key that should be cryptographically random
     *
     * @throws CryptoException If aes encryption fails.
     *
     * @return Bytes Encrypted bytes with a 12-byte IV on the front and a 16-byte tag on the end
     */
    private static function encrypt(Bytes $plaintext, Bytes $key, Rng $rng): Bytes
    {
        return self::encryptWithIv($plaintext, $key, $rng->randomBytes(12));
    }

    /**
     * Encrypts given plaintext with the provided key and IV.
     *
     * @param Bytes $plaintext Bytes to encrypt
     * @param Bytes $key 32-byte secret key that should be cryptographically random
     * @param Bytes $iv IV to use for encryption
     *
     * @throws CryptoException If the iv is not of the correct length or if aes encryption fails.
     *
     * @return Bytes Encrypted bytes with a 12-byte IV on the front and a 16-byte tag on the end
     */
    private static function encryptWithIv(Bytes $plaintext, Bytes $key, Bytes $iv): Bytes
    {
        if ($iv->length() != AesConstants::IV_LEN) {
            throw new CryptoException("The IV passed was not the correct length.");
        }
        $ciphertext = openssl_encrypt(
            $plaintext->getByteString(),
            'aes-256-gcm',
            $key->getByteString(),
            OPENSSL_RAW_DATA,
            $iv->getByteString(),
            $tag
        );
        if ($ciphertext === false) {
            throw new CryptoException('Encrypting the input failed.');
        }
        return new Bytes($iv->getByteString() . $ciphertext . $tag);
    }

    /**
     * Takes a string of arbitrary bytes and decrypts it using a given key.
     *
     * @param Bytes $ciphertext The ciphertext to decrypt
     * @param Bytes $key The 32-byte secret key
     *
     * @throws CryptoException If the ciphertext could not be decrypted.
     *
     * @return Bytes The plaintext, which is arbitrary bytes.
     */
    private static function decrypt(Bytes $ciphertext, Bytes $key): Bytes
    {
        // The length of the ciphertext cannot possibly be shorter than IV plus the tag.
        if ($ciphertext->length() <= AesConstants::IV_LEN + AesConstants::TAG_LEN) {
            throw new CryptoException('The ciphertext was not well formed.');
        }

        $authTag = $ciphertext->byteSlice(-AesConstants::TAG_LEN);
        $iv = $ciphertext->byteSlice(0, AesConstants::IV_LEN);
        //mutate the ciphertext to be just the aes encrypted bytes, without the IV or the tag.
        $ciphertext = $ciphertext->byteSlice(AesConstants::IV_LEN, -AesConstants::TAG_LEN);

        $plaintext = openssl_decrypt(
            $ciphertext->getByteString(),
            'aes-256-gcm',
            $key->getByteString(),
            OPENSSL_RAW_DATA,
            $iv->getByteString(),
            $authTag->getByteString()
        );

        if ($plaintext === false) {
            throw new CryptoException('AES decryption failed.');
        }

        return new Bytes($plaintext);
    }

    /**
     * Encrypts a document.
     *
     * @param Bytes $document Document bytes to encrypt
     * @param string $tenantId Tenant performing the encryption
     * @param Bytes $dek Document encryption key
     * @param Rng Cryptographically-secure way to generate random bytes
     *
     * @throws CryptoException If the AES encrypt fails.
     *
     * @return Bytes Encrypted document bytes
     */
    private static function encryptDocument(Bytes $document, string $tenantId, Bytes $dek, Rng $rng): Bytes
    {
        $header = self::generateHeader($dek, $tenantId, $rng);
        $encrypted = self::encrypt($document, $dek, $rng);
        return $header->concat($encrypted);
    }

    /**
     * Decrypts an encrypted document.
     *
     * @param Bytes $document Encrypted bytes to decrypt
     * @param Bytes $dek Document encryption key used to encrypt the document
     *
     * @throws CryptoException If the header was corrupt or if decryption fails.
     *
     * @return Bytes Decrypted document
     */
    private static function decryptDocument(Bytes $document, Bytes $dek): Bytes
    {
        $documentParts = self::splitDocument($document);
        $headerBytes = $documentParts->getHeader();
        $documentHeader = new V3DocumentHeader();
        $documentHeader->mergeFromString($headerBytes->getByteString());
        $ciphertext = $documentParts->getCiphertext();
        if (!self::verifySignature($dek, $documentHeader)) {
            throw new CryptoException("The signature computed did not match. The document key is likely incorrect.");
        } else {
            return self::decrypt($ciphertext, $dek);
        }
    }

    /**
     * Splits an IronCore encrypted document into its component pieces.
     *
     * @param Bytes $document IronCore encrypted document
     *
     * @throws CryptoException If the document cannot be split into its components.
     *
     * @return DocumentParts Object containing the distinct parts of the document
     */
    private static function splitDocument(Bytes $document): DocumentParts
    {
        $fixedPreamble = $document->byteSlice(0, AesConstants::DOCUMENT_HEADER_META_LENGTH);
        if (!self::verifyPreamble($fixedPreamble)) {
            throw new CryptoException("Provided bytes were not an IronCore encrypted document.");
        } else {
            $headerLength = self::getHeaderSize($fixedPreamble);
            $header = $document->byteSlice(AesConstants::DOCUMENT_HEADER_META_LENGTH, $headerLength);
            $ciphertext = $document->byteSlice(AesConstants::DOCUMENT_HEADER_META_LENGTH + $headerLength);
            return new DocumentParts($fixedPreamble, $header, $ciphertext);
        }
    }

    /**
     * Verifies that the preamble is the correct length and version, contains the string "IRON",
     * and indicates a valid header length.
     *
     * @param Bytes $preamble The first 7 bytes of an IronCore encrypted document
     *
     * @return bool `true` if the preamble is valid
     */
    private static function verifyPreamble(Bytes $preamble): bool
    {
        return $preamble->length() === AesConstants::DOCUMENT_HEADER_META_LENGTH
            && $preamble->getAtIndex(0) == self::getCurrentDocumentHeaderVersion()
            && self::containsIroncoreMagic($preamble)
            && self::getHeaderSize($preamble) >= 0;
    }

    /**
     * Verifies that bytes 2-5 are the IronCore magic string.
     *
     * @param Bytes $bytes Bytes to check
     *
     * @return bool `true` if the bytes are the correct form.
     */
    private static function containsIroncoreMagic(Bytes $bytes): bool
    {
        return $bytes->byteSlice(1, 4) == self::getDocumentMagic();
    }

    /**
     * Converts bytes 6 and 7 of the fixed preamble to an integer that
     * represents the length of the header.
     *
     * @param Bytes $preamble The first 7 bytes of an IronCore encrypted document
     *
     * @return int The length of the IronCore header
     */
    private static function getHeaderSize(Bytes $preamble): int
    {
        // Unpack bytes 5 and 6 using format "n": unsigned short (always 16 bit, big endian byte order)
        return unpack("n", $preamble->byteSlice(5, 2)->getByteString())[1];
    }

    /**
     * Generates a header to mark the encrypted document as ours.
     *
     * Current version is as follows: VERSION_NUMBER (1 bytes, fixed at `3`),
     * IRONCORE_MAGIC (4 bytes, "IRON" in ASCII), HEADER_LENGTH (2 bytes Uint16),
     * PROTOBUF_HEADER_DATA (variable bytes)
     *
     * @param Bytes $dek Document encryption key to use for signing
     * @param string $tenantId Tenant making the request
     * @param Rng Cryptographically-secure way to generate random bytes
     *
     * @throws CryptoException If the header is too long or if the signature generation fails.
     *
     * @return Bytes Bytes of header
     */
    public static function generateHeader(Bytes $dek, string $tenantId, Rng $rng): Bytes
    {
        $headerProto = self::createHeaderProto($dek, $tenantId, $rng);
        $headerBytes = $headerProto->serializeToString();
        $headerLength = strlen($headerBytes);
        if ($headerLength > AesConstants::MAX_HEADER_SIZE) {
            throw new CryptoException("The header is too large. It is $headerLength bytes long.");
        }
        // pack header length using format "n": unsigned short (always 16 bit, big endian byte order)
        $headerSize = pack("n", $headerLength);
        // Using the `.` concatenation stringifies the int, so we explicitly pack it to an unsigned char
        $documentVersion = self::getCurrentDocumentHeaderVersion();
        return new Bytes($documentVersion->getByteString() . self::getDocumentMagic()->getByteString() .
            $headerSize . $headerBytes);
    }

    /**
     * Make a V3DocumentHeader with a SaaSShieldHeader and a generated signature.
     *
     * @param Bytes $dek Document encryption key to use for signing
     * @param string $tenantId Tenant making the request
     * @param Bytes $iv Option IV to use when signing. If not present, a random IV is generated.
     *
     * @return V3DocumentHeader Signed document header
     */
    public static function createHeaderProto(
        Bytes $dek,
        string $tenantId,
        Rng $rng,
        ?Bytes $iv = null
    ): V3DocumentHeader {
        if ($iv == null) {
            $iv = $rng->randomBytes(AesConstants::IV_LEN);
        }
        $saasHeader = new SaaSShieldHeader();
        $saasHeader->setTenantId($tenantId);
        $signature = self::generateSignature($dek, $iv, $saasHeader);
        $v3Header = new V3DocumentHeader();
        $v3Header->setSig($signature->getSignatureBytes()->getByteString());
        $v3Header->setSaasShield($saasHeader);
        return $v3Header;
    }

    /**
     * Generates a signature over the header using the provided document encryption key and IV.
     *
     * @param Bytes $dek Document encryption key to use for signing
     * @param Bytes $iv IV to use for signing
     * @param SaaSShieldHeader $header Header to sign over
     *
     * @return V3HeaderSignature Header signature
     */
    public static function generateSignature(Bytes $dek, Bytes $iv, SaaSShieldHeader $header): V3HeaderSignature
    {
        $headerBytes = new Bytes($header->serializeToString());
        $encryptedHeaderValue = self::encryptWithIv($headerBytes, $dek, $iv);
        $tag = $encryptedHeaderValue->byteSlice(-AesConstants::TAG_LEN);
        return new V3HeaderSignature($iv, $tag);
    }

    /**
     * Verifies a document header's signature.
     *
     * @param Bytes $dek Document encryption key used when generating the signature
     * @param V3DocumentHeader $header Header with signature to verify
     * @return bool `true` if the header's signature was successfully verified
     */
    public static function verifySignature(Bytes $dek, V3DocumentHeader $header): bool
    {
        if (!$header->hasSaasShield()) {
            return false;
        }
        $headerSigBytes = new Bytes($header->getSig());
        $knownSig = V3HeaderSignature::fromBytes($headerSigBytes);
        $candidateSig = self::generateSignature($dek, $knownSig->getIv(), $header->getSaasShield());
        return $knownSig == $candidateSig;
    }
}
