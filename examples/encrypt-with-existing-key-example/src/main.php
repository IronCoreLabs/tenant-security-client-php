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

// default to "tenant-gcp-l". Override by setting the TENANT_ID environment variable
$TENANT_ID = getenv("TENANT_ID");
if ($TENANT_ID == null) {
    $TENANT_ID = "tenant-gcp-l";
}
echo "Using tenant $TENANT_ID\n";


//
// Step 1: Encrypt a customer record and store it
//

// Create metadata used to associate this document to a tenant, name the document, and
// identify the service or user making the call
$metadata = new RequestMetadata($TENANT_ID, new IclFields("serviceOrUserId", "PII"), []);

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

//
// Step 2: Retrieve a customer record and decrypt it
//

// retrieve the EDEK and encryptedDocument from your persistence layer
$retrievedEncryptedDocument = new EncryptedDocument($encryptedDocument, $edek);

try {
    $decryptedPlaintext = $tenantSecurityClient->decrypt($retrievedEncryptedDocument, $metadata);
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
    exit(1);
}
$decryptedValues = $decryptedPlaintext->getDecryptedFields();

// 
// Step 3: Update the customer record, re-encrypt it with the existing key, and store the new fields
//

$decryptedValues["address"] = new Bytes("12345 N Montana Ave Helena, MT, 59601");
$newDocument = new PlaintextDocument($decryptedValues, $edek);

try {
    $reencryptResult = $tenantSecurityClient->encryptWithExistingKey($newDocument, $metadata);
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
    exit(1);
}
// persist the reencryptedDocument to your persistence layer (EDEK is unchanged)
$reencryptedDocument = $reencryptResult->getEncryptedFields();

// 
// Step 4: Retrieve the EDEK and encryptedDocument and decrypt
//

// unchanged original EDEK
$retrievedEncryptedDocument = new EncryptedDocument($reencryptedDocument, $edek);

try {
    $decryptedPlaintext = $tenantSecurityClient->decrypt($retrievedEncryptedDocument, $metadata);
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
    exit(1);
}
$decryptedValues = $decryptedPlaintext->getDecryptedFields();

echo ("Decrypted SSN: " . $decryptedValues["ssn"]->getByteString() . "\n");
// Note the updated value
echo ("Decrypted address: " . $decryptedValues["address"]->getByteString() . "\n");
echo ("Decrypted name: " . $decryptedValues["name"]->getByteString() . "\n");
