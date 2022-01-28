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

$tenantSecurityClient = new TenantSecurityClient($TSP_ADDRESS, $API_KEY);

$startingTenant = "tenant-gcp";

// Create metadata used to associate this document to a GCP tenant, name the document, and
// identify the service or user making the call
$metadata = new RequestMetadata($startingTenant, new IclFields("serviceOrUserId", "PII"), []);

//
// Part 1: Encrypt a file for the GCP tenant, using the filesystem for persistence
//

$sourceFilename = "success.jpg";
$toEncryptBytes = new Bytes(file_get_contents($sourceFilename));
$toEncrypt = ["file" => $toEncryptBytes];
try {
    // Encrypt the file to the GCP tenant
    $encryptedResults = $tenantSecurityClient->encrypt($toEncrypt, $metadata);
    // Write the encrypted file and the encrypted key to the filesystem
    file_put_contents("$sourceFilename.enc", $encryptedResults->getEncryptedFields()["file"]->getByteString());
    echo ("Wrote encrypted file to $sourceFilename.enc\n");
    file_put_contents("$sourceFilename.edek", $encryptedResults->getEdek()->getByteString());
    echo ("Wrote tenant-gcp edek to $sourceFilename.edek\n");
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
    exit(1);
}

//
// Part 2: Re-key the EDEK to the AWS tenant
//
try {
    // Some time later... read the EDEK from the disk (don't need the encrypted file)
    $encryptedDek = new Bytes(file_get_contents("$sourceFilename.edek"));
    $newTenant = "tenant-aws";
    // Re-key the EDEK to the AWS tenant
    $newEdek = $tenantSecurityClient->rekeyEdek($encryptedDek, $newTenant, $metadata);
    // Replace the stored EDEK with the newly re-keyed one
    file_put_contents("$sourceFilename.edek", $newEdek->getByteString());
    echo ("Wrote tenant-aws edek to $sourceFilename.edek\n");
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
    exit(1);
}

// 
// Part 3: Decrypt the document for the AWS tenant
try {
    // Some time later... read the file from the disk
    $encryptedBytes = file_get_contents("$sourceFilename.enc");
    $encryptedDek = file_get_contents("$sourceFilename.edek");

    $fileAndEdek = new EncryptedDocument(["file" => new Bytes($encryptedBytes)], new Bytes($encryptedDek));

    $newMetadata = new RequestMetadata($newTenant, new IclFields("serviceOrUserId", "PII"), []);

    // Decrypt for AWS tenant
    $roundtripFile = $tenantSecurityClient->decrypt($fileAndEdek, $newMetadata);
    echo ("Decrypted file for tenant-aws\n");

    // Write the decrypted file back to the filesystem
    file_put_contents("decrypted.jpg", $roundtripFile->getDecryptedFields()["file"]->getByteString());
    echo ("Wrote decrypted file to decrypted.jpg\n");
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
    exit(1);
}
