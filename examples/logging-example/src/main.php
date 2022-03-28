<?php

declare(strict_types=1);

namespace IronCore;

use IronCore\Exception\TenantSecurityException;
use IronCore\SecurityEvents\AdminEvent;
use IronCore\SecurityEvents\UserEvent;

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


// Example 1: logging a user-related event
// Create metadata about the event. This example populates all possible fields with a value, 
// including the customFields map. Sets the timestamp to 5 seconds before the current data/time.

$tenantSecurityClient = new TenantSecurityClient($TSP_ADDRESS, $API_KEY);

$customFields = ["field1" => "gumby", "field2" => "gumby"];
$metadata = new EventMetadata(
    $TENANT_ID,
    new IclFields("userId1", "PII", "127.0.0.1", "object1", "Rq8675309"),
    $customFields,
    (time() - 5) * 1000
);

try {
    $tenantSecurityClient->logSecurityEvent(UserEvent::login(), $metadata);
    echo "Successfully logged user login event.\n";
} catch (TenantSecurityException $e) {
    echo "Failed to log security event.\n";
    echo "Error message: " . $e->getMessage() . "\n";
}


// Example 2: logging an admin-related event
// This example adds minimal metadata for the event. The timestamp should be roughly
// 5 seconds after the one on the previous event.

$tenantSecurityClient = new TenantSecurityClient($TSP_ADDRESS, $API_KEY);

$metadata = new EventMetadata($TENANT_ID, new IclFields("userId1"), [], time() * 1000);

try {
    $tenantSecurityClient->logSecurityEvent(AdminEvent::add(), $metadata);
    echo "Successfully logged admin add event.\n";
} catch (TenantSecurityException $e) {
    echo "Failed to log security event.\n";
    echo "Error message: " . $e->getMessage() . "\n";
}

// You should be able to see that these two events were delivered in the TSP
// logs. If you have access to the example tenant's SIEM, you can see these
// events in their logs.