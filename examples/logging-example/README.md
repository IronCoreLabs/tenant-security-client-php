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

```bash
tenant-security-proxy_1      | {"service":"proxy","message":"Security Event Received","level":"INFO","timestamp":"2022-03-28T18:22:42.699357522+00:00","tenant_id":"tenant-gcp-l","rayid":"syBKJMj8xOI5zSAJ"}
tenant-security-proxy_1      | {"service":"proxy","message":"{\"iclFields\":{\"dataLabel\":\"PII\",\"requestId\":\"Rq8675309\",\"requestingId\":\"userId1\",\"sourceIp\":\"127.0.0.1\",\"objectId\":\"object1\",\"event\":\"USER_LOGIN\"},\"customFields\":{\"field2\":\"gumby\",\"field1\":\"gumby\"}}","level":"INFO","timestamp":"2022-03-28T18:22:42.699385813+00:00","tenant_id":"tenant-gcp-l","rayid":"syBKJMj8xOI5zSAJ"}
tenant-security-proxy_1      | {"service":"proxy","message":"Security Event Received","level":"INFO","timestamp":"2022-03-28T18:22:42.700447605+00:00","tenant_id":"tenant-gcp-l","rayid":"iojQAIWHre2yC-iU"}
tenant-security-proxy_1      | {"service":"proxy","message":"{\"iclFields\":{\"dataLabel\":null,\"requestId\":null,\"requestingId\":\"userId1\",\"sourceIp\":null,\"objectId\":null,\"event\":\"ADMIN_ADD\"},\"customFields\":{}}","level":"INFO","timestamp":"2022-03-28T18:22:42.700471497+00:00","tenant_id":"tenant-gcp-l","rayid":"iojQAIWHre2yC-iU"}
tenant-security-logdriver_1  | {"service":"logdriver","message":"Making request to Stackdriver to write 2 log entries.","level":"INFO","timestamp":"2022-03-28T18:22:42.800555550+00:00","tenant_id":"tenant-gcp-l"}
tenant-security-logdriver_1  | {"service":"logdriver","message":"Successfully wrote 2 log entries to Stackdriver.","level":"INFO","timestamp":"2022-03-28T18:22:42.905105143+00:00","tenant_id":"tenant-gcp-l"}
```

This shows the TSP receiving these events and sending them to LogDriver, which then batches them up together and sends them to Stackdriver (the configured log sink for `tenant-gcp-l`).

If you would like to experiment with a different tenant, just do:

```bash
export TENANT_ID=<selected-tenant-ID>
php src/main.php
```

The list of available tenants is listed in the [README.md](../README.md) in the parent directory.

## Additional Resources

If you would like some more in-depth information, our website features a section of technical
documentation about the [SaaS Shield product](https://ironcorelabs.com/docs/saas-shield/).
