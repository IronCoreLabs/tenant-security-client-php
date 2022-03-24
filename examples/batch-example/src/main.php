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

// Create metadata used to associate this document to a tenant, name the document, and
// identify the service or user making the call
$metadata = new RequestMetadata($TENANT_ID, new IclFields("serviceOrUserId", "PII"), []);

$cust1Record = [
    "id" => new Bytes("19828392"),
];
$cust2Record = [
    "id" => new Bytes("12387643"),
];
$documents = ["1" => $cust1Record, "2" => $cust2Record];

$tenantSecurityClient = new TenantSecurityClient($TSP_ADDRESS, $API_KEY);

// Encrypt the documents 
try {
    $encryptionResults = $tenantSecurityClient->batchEncrypt($documents, $metadata);
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
    exit(1);
}

$encryptionDocuments = $encryptedResults->getEncryptedDocuments();

// Decrypt the documents
try {
    $decryptionResults = $tenantSecurityClient->batchDecrypt($encryptedDocuments, $metadata);
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
    exit(1);
}

$decryptedDocuments = $decryptionResults->getPlaintextDocuments();
