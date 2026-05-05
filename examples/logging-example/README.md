# Logging Example

In order to run this example, you need to be running a _Tenant Security Proxy_ (TSP) and _Tenant Security LogDriver_ (LD) on your machine.
Check the [README.md](../README.md) file in the parent directory to see how to start the TSP, if you haven't done so
yet.

Once the TSP and LD are running, you can experiment with this example php program. It illustrates the basics of how
to use the Tenant Security Client (TSC) SDK to log security events. The example code shows two scenarios:

- logging a login security event with complete metadata
- logging an admin add security event with minimal metadata

To run the example, you will need to have a PHP 7.4+ and composer installed on your computer.

```bash
export API_KEY='0WUaXesNgbTAuLwn'
composer update
php src/main.php
```

We've assigned an API key for you, but in production you will make your own and edit the TSP
configuration with it. This should produce output like:

```
Using tenant tenant-gcp-l
Successfully logged user login event.
Successfully logged admin add event.
```

The output "Successfully logged user login event." is printed after successfully sending the login event
to the TSP. Same thing with "Successfully logged admin add event." but for the add event.

If you look in the TSP/LD logs you should see something like:

```
tenant-security-proxy-1      | {"contexts":"request","level":"INFO","service":"proxy","timestamp":"2026-05-05T18:02:22.022940Z","message":"Security Event Received","name":"request","ray_id":"ssxlOOMj0isKWtuQ","tenant_id":"tenant-gcp-l"}
tenant-security-proxy-1      | {"contexts":"request","level":"INFO","service":"proxy","timestamp":"2026-05-05T18:02:22.023068Z","message":"{\"iclFields\":{\"dataLabel\":\"PII\",\"requestId\":\"Rq8675309\",\"requestingId\":\"userId1\",\"sourceIp\":\"127.0.0.1\",\"objectId\":\"object1\",\"event\":\"USER_LOGIN\"},\"customFields\":{\"field1\":\"gumby\",\"field2\":\"gumby\"}}","name":"request","ray_id":"ssxlOOMj0isKWtuQ","tenant_id":"tenant-gcp-l"}
tenant-security-proxy-1      | {"contexts":"request","level":"INFO","service":"proxy","timestamp":"2026-05-05T18:02:22.026257Z","message":"Security Event Received","name":"request","ray_id":"GFYximi8b0xXLC72","tenant_id":"tenant-gcp-l"}
tenant-security-proxy-1      | {"contexts":"request","level":"INFO","service":"proxy","timestamp":"2026-05-05T18:02:22.026271Z","message":"{\"iclFields\":{\"dataLabel\":null,\"requestId\":null,\"requestingId\":\"userId1\",\"sourceIp\":null,\"objectId\":null,\"event\":\"ADMIN_ADD\"},\"customFields\":{}}","name":"request","ray_id":"GFYximi8b0xXLC72","tenant_id":"tenant-gcp-l"}
tenant-security-logdriver-1  | {"contexts":"main;batching;tenant","level":"INFO","service":"logdriver","timestamp":"2026-05-05T18:02:24.087980548Z","message":"BATCH: 2 log events received for an unknown tenant. Using a stdout logger for this tenant.","name":"tenant","tenant_id":"tenant-gcp-l"}
tenant-security-logdriver-1  | {"contexts":"main;batching;stdout client;write-entries","level":"INFO","service":"logdriver","timestamp":"2026-05-05T18:02:24.088209173Z","message":"{\"tenantId\":\"tenant-gcp-l\",\"timestamp\":\"2026-05-05T18:02:17Z\",\"iclFields\":{\"event\":\"USER_LOGIN\",\"logdriverRayId\":\"kHemkWFAGAvnNDr0\",\"sourceIp\":\"127.0.0.1\",\"tspRayId\":\"ray_id\",\"objectId\":\"object1\",\"requestId\":\"Rq8675309\",\"requestingId\":\"userId1\",\"dataLabel\":\"PII\"},\"customFields\":{\"field2\":\"gumby\",\"field1\":\"gumby\"}}","name":"write-entries"}
tenant-security-logdriver-1  | {"contexts":"main;batching;stdout client;write-entries","level":"INFO","service":"logdriver","timestamp":"2026-05-05T18:02:24.088275131Z","message":"{\"tenantId\":\"tenant-gcp-l\",\"timestamp\":\"2026-05-05T18:02:22Z\",\"iclFields\":{\"logdriverRayId\":\"eH4gGBAcJTP0XP39\",\"tspRayId\":\"ray_id\",\"event\":\"ADMIN_ADD\",\"requestingId\":\"userId1\"},\"customFields\":{}}","name":"write-entries"}
```

This shows the TSP receiving these events, batching them up together, and sending them successfully to Logdriver. Because this tenant does not have a log sink configured,
the security events will be output to Logdriver's stdout logs.

If you would like to experiment with a different tenant, just do:

```bash
export TENANT_ID=<selected-tenant-ID>
php src/main.php
```

The list of available tenants is listed in the [README.md](../README.md) in the parent directory.

## Additional Resources

If you would like some more in-depth information, our website features a section of technical
documentation about the [SaaS Shield product](https://ironcorelabs.com/docs/saas-shield/).
