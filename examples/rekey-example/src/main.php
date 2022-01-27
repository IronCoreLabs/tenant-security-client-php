<?php

declare(strict_types=1);

namespace IronCore;

use Exception;

require_once __DIR__ . '/../vendor/autoload.php';

$TSP_ADDRESS = "http://localhost:32804";
// In order to communicate with the TSP, you need a matching API_KEY. Find the
// right value from the end of the TSP configuration file, and set the API_KEY
// environment variable to that value.
$API_KEY = getenv("API_KEY");
if ($API_KEY == null) {
    echo ("Must set the API_KEY environment variable.\n");
    exit(1);
}

//
// Example 1: encrypting/decrypting a customer record
//

$startingTenant = "tenant-gcp";

// Create metadata used to associate this document to a tenant, name the document, and
// identify the service or user making the call
$metadata = new RequestMetadata($startingTenant, new IclFields("serviceOrUserId", "PII"), []);

// Create a map containing your data
$custRecord = [
    "ssn" => new Bytes("000-12-2345"),
    "address" => new Bytes("2825-519 Stone Creek Rd, Bozeman, MT 59715"),
    "name" => new Bytes("Jim Bridger")
];

$tenantSecurityClient = new TenantSecurityClient($TSP_ADDRESS, $API_KEY);
try {
    $encryptedResults = $tenantSecurityClient->encrypt($custRecord, $metadata);
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
    exit(1);
}
// persist the EDEK and encryptedDocument to your persistence layer
$edek = $encryptedResults->getEdek();
$encryptedDocument = $encryptedResults->getEncryptedFields();
// un-comment if you want to print out the encrypted data
// echo ("Encrypted SSN: " . $encryptedDocument["ssn"]->getHexString() . "\n");
// echo ("Encrypted address: " . $encryptedDocument["address"]->getHexString() . "\n");
// echo ("Encrypted name: " . $encryptedDocument["name"]->getHexString() . "\n");

// retrieve the EDEK and encryptedDocument from your persistence layer
$retrievedEncryptedDocument = new EncryptedDocument($encryptedDocument, $edek);

try {
    $decryptedPlaintext = $tenantSecurityClient->decrypt($retrievedEncryptedDocument, $metadata);
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
    exit(1);
}
$decryptedValues = $decryptedPlaintext->getDecryptedFields();


echo ("Decrypted SSN: " . $decryptedValues["ssn"]->getByteString() . "\n");
echo ("Decrypted address: " . $decryptedValues["address"]->getByteString() . "\n");
echo ("Decrypted name: " . $decryptedValues["name"]->getByteString() . "\n");

//
// Part 1: encrypting/decrypting a file, using the filesystem for persistence
//

$sourceFilename = "success.jpg";
$toEncryptBytes = new Bytes(file_get_contents($sourceFilename));
$toEncrypt = ["file" => $toEncryptBytes];
try {
    //Encrypt the file.
    $encryptedResults = $tenantSecurityClient->encrypt($toEncrypt, $metadata);
    // write the encrypted file and the encrypted key to the filesystem
    file_put_contents("$sourceFilename.enc", $encryptedResults->getEncryptedFields()["file"]->getByteString());
    echo ("Wrote encrypted file to $sourceFilename.enc\n");
    file_put_contents("$sourceFilename.edek", $encryptedResults->getEdek()->getByteString());
    echo ("Wrote edek to $sourceFilename.edek\n");
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
    exit(1);
}

//
// Part 2: re-key the EDEK to a new tenant
//
try {
    // some time later... read the EDEK from the disk
    $encryptedDek = new Bytes(file_get_contents("$sourceFilename.edek"));
    $newTenant = "tenant-aws";
    // re-key the EDEK to an AWS tenant, using the same metadata for the call
    $newEdek = $tenantSecurityClient->rekeyEdek($encryptedDek, $newTenant, $metadata);
    // replace the stored EDEK with the newly re-keyed one
    file_put_contents("$sourceFilename.edek", $newEdek->getByteString());
    echo ("Replaced edek $sourceFilename.edek\n");
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
    exit(1);
}

// 
// Part 3: decrypt the document for the AWS tenant
try {
    // some time later... read the file from the disk
    $encryptedBytes = file_get_contents("$sourceFilename.enc");
    $encryptedDek = file_get_contents("$sourceFilename.edek");

    $fileAndEdek = new EncryptedDocument(["file" => new Bytes($encryptedBytes)], new Bytes($encryptedDek));

    $metadata = new RequestMetadata($newTenant, new IclFields("serviceOrUserId", "PII"), []);

    // decrypt
    $roundtripFile = $tenantSecurityClient->decrypt($fileAndEdek, $metadata);

    // write the decrypted file back to the filesystem
    file_put_contents("decrypted.jpg", $roundtripFile->getDecryptedFields()["file"]->getByteString());
    echo ("Wrote decrypted file to decrypted.jpg\n");
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
    exit(1);
}
