<?php

declare(strict_types=1);

namespace IronCore\Crypto;

use IronCore\Bytes;
use IronCore\Exception\CryptoException;
use PHPUnit\Framework\TestCase;
use Proto\IronCoreLabs\V3DocumentHeader;
use UnexpectedValueException;

require("TestRng.php");

final class AesTest extends TestCase
{
    use Aes;

    private static $knownGoodEncryptedValueHexString = "0349524f4e016c0a1c3130eaf8ff88c1a08df550095522aebfdc7b0d060d3adad8836fea7e1acb020ac80274656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e74496474656e616e744964bb54218111033f5c68c92feb8fae88c255cc56e902becdfde679defa2628950beb966e0e43d27f42dcdbd98587e8bf5f8458411760fb72ca4442ae79877da90dff7de6df43e549df3085aae5f55f05aa37cdd045ffa7";
    private $knownDek;
    private $knownDek2;

    public function setUp(): void
    {
        $this->knownDek = self::hexToBytes("3939393939393939393939393939393939393939393939393939393939393939");
        $this->knownDek2 = self::hexToBytes("3838383838383838383838383838383838383838383838383838383838383838");
    }

    private static function callAesMethod(string $name, array $args)
    {
        $class = new \ReflectionClass('IronCore\Crypto\Aes');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        // All methods in this class are static, so we can pass null for object
        $result = $method->invokeArgs(null, $args);
        return $result;
    }

    public function testEncryptDecryptRoundtrip(): void
    {
        $rng = new TestRng("someseed");
        $plaintext = new Bytes("This is a non base64 string.");
        $key = $rng->randomBytes(32);
        $encryptedResult = self::encryptInternal($plaintext, $key, new TestRng("hello"));
        $decryptedResult = self::decryptInternal($encryptedResult, $key);
        $this->assertEquals($plaintext, $decryptedResult);
    }

    public function testSignVerify(): void
    {
        $rng = new TestRng("seed");
        $dek = $rng->randomBytes(32);
        $proto = self::createHeaderProto($dek, "This is my tenant id", $rng);
        $this->assertTrue(self::verifySignature($dek, $proto));
    }

    // This is a known encrypted value
    public function testDecryptingKnownEncryptedValue(): void
    {
        $encryptedDocument = self::hexToBytes(
            self::$knownGoodEncryptedValueHexString
        );
        $decryptedBytes = self::decryptDocument($encryptedDocument, $this->knownDek);
        $this->assertEquals($decryptedBytes->getByteString(), "I have a fever and the only cure is nine nine nine nine...");
    }

    // This test is to ensure that the production of the proto is identical to a known value from the java sdk.
    public function testKnownHeaderProtoFromJava(): void
    {
        $dek = self::hexToBytes("000102030405060708090A0B0C0D0E0F101112131415161718191A1B1C1D1E1F");
        $iv = self::hexToBytes("3171EF3C899F875E595C2213");
        $expectedHexResult = strtolower("0A1C3171EF3C899F875E595C2213CACF9287C78CF196458CD690544980C71A0A0A0874656E616E744964");
        $result = self::createHeaderProto($dek, "tenantId", new TestRng("test"), $iv);
        $this->assertEquals(AesTest::bytesToHex(new Bytes($result->serializeToString())), $expectedHexResult);
    }

    // This is a known encrypted value with the last byte changed
    public function testDecryptingBadTag(): void
    {
        $hexString = substr(self::$knownGoodEncryptedValueHexString, 0, -2) . "00";
        $encryptedDocument = self::hexToBytes($hexString);
        $this->expectException(CryptoException::class);
        $this->expectExceptionMessage("AES decryption failed.");
        self::decryptDocument($encryptedDocument, $this->knownDek);
    }

    // This is an incorrect preamble.
    public function testDecryptInvalidDocument(): void
    {
        $hexString = "00000000000000";
        $encryptedDocument = self::hexToBytes($hexString);
        $this->expectException(CryptoException::class);
        $this->expectExceptionMessage("Provided bytes were not an IronCore encrypted document.");
        self::decryptDocument($encryptedDocument, $this->knownDek);
    }

    // This is an incorrect preamble.
    public function testDecryptInvalidDocumentIncorrectLength(): void
    {
        $hexString = "00000000000100"; //Length of 256
        $encryptedDocument = self::hexToBytes($hexString);
        $this->expectException(CryptoException::class);
        $this->expectExceptionMessage("Provided bytes were not an IronCore encrypted document.");
        self::decryptDocument($encryptedDocument, $this->knownDek);
    }

    public function testVerifyWithWrongDek(): void
    {
        $header = self::createHeaderProto($this->knownDek, "tenant", new TestRng("johnny"));
        $this->assertFalse(self::verifySignature($this->knownDek2, $header));
    }

    public function testDecryptDocumentWithCorruptHeader(): void
    {
        $corruptDocument = substr_replace(self::$knownGoodEncryptedValueHexString, "00000000", 7, 8);
        $this->expectException(CryptoException::class);
        $this->expectExceptionMessage("Provided bytes were not an IronCore encrypted document.");
        self::decryptDocument(self::hexToBytes($corruptDocument), $this->knownDek);
    }

    private static function hexToBytes(string $hex): Bytes
    {
        return new Bytes(pack("H*", $hex));
    }

    private static function bytesToHex(Bytes $bytes): string
    {
        $unpackResult = unpack("H*", $bytes->getByteString());
        if ($unpackResult === false) {
            throw new UnexpectedValueException("Value could not be unpacked. Maybe it wasn't hex?");
        }
        return $unpackResult[1];
    }

    public function testEncryptWithBadIv(): void
    {
        $rng = new TestRng("someseed");
        $plaintext = new Bytes("This is a non base64 string.");
        $key = $rng->randomBytes(32);
        $badIv = new Bytes("foobar");
        $this->expectException(CryptoException::class);
        $this->expectExceptionMessage("The IV passed was not the correct length.");
        self::callAesMethod('encryptWithIv', [$plaintext, $key, $badIv]);
    }

    public function testDecryptTooShort(): void
    {
        $rng = new TestRng("someseed");
        $key = $rng->randomBytes(32);
        $badCiphertext = new Bytes("foo");
        $this->expectException(CryptoException::class);
        $this->expectExceptionMessage("The ciphertext was not well formed.");
        self::decryptInternal($badCiphertext, $key);
    }

    public function testRoundtripDocument(): void
    {
        $rng = new TestRng("someseed");
        $dek = $rng->randomBytes(32);
        $document = new Bytes("bytes");
        $tenantId = "tenant";
        $encrypted = self::encryptDocument($document, $tenantId, $dek, $rng);
        $decrypted = self::decryptDocument($encrypted, $dek);
        $this->assertEquals($decrypted, $document);
    }

    public function testDoubleEncryptDocument(): void
    {
        $rng = new TestRng("someseed");
        $dek = $rng->randomBytes(32);
        $document = new Bytes("bytes");
        $tenantId = "tenant";
        $encrypted = self::encryptDocument($document, $tenantId, $dek, $rng);
        $this->expectException(CryptoException::class);
        $this->expectExceptionMessage("The provided document is already IronCore encrypted.");
        self::encryptDocument($encrypted, $tenantId, $dek, $rng);
    }

    public function testDecryptBadDocument(): void
    {
        $rng = new TestRng("someseed");
        $dek = $rng->randomBytes(32);
        $document = new Bytes("bytes");
        $tenantId = "tenant";
        $encrypted = self::encryptDocument($document, $tenantId, $dek, $rng);
        $badDek = new Bytes("bar");
        $this->expectException(CryptoException::class);
        $this->expectExceptionMessage("The signature computed did not match. The document key is likely incorrect.");
        self::decryptDocument($encrypted, $badDek);
    }

    public function testGenerateHeaderTooLarge(): void
    {
        $rng = new TestRng("someseed");
        $dek = $rng->randomBytes(32);
        $tenantId = str_repeat("g", 100000);
        $this->expectException(CryptoException::class);
        $this->expectExceptionMessage("The header is too large.");
        self::generateHeader($dek, $tenantId, $rng);
    }

    public function testVerifyWrongHeader(): void
    {
        // No SaasShieldHeader set
        $header = new V3DocumentHeader();
        $verify = self::verifySignature(new Bytes("dek"), $header);
        $this->assertFalse($verify);
    }
}
