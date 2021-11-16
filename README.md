[![Latest Stable Version](http://poser.pugx.org/ironcorelabs/tenant-security-client-php/v)](https://packagist.org/packages/ironcorelabs/tenant-security-client-php)
[![PHP CI](https://github.com/IronCoreLabs/tenant-security-client-php/actions/workflows/ci.yaml/badge.svg)](https://github.com/IronCoreLabs/tenant-security-client-php/actions/workflows/ci.yaml)
[![License](http://poser.pugx.org/ironcorelabs/tenant-security-client-php/license)](https://packagist.org/packages/ironcorelabs/tenant-security-client-php)

# Tenant Security Client PHP Library

A PHP client for implementing CMK within a vendor's infrastructure. Makes requests through an
IronCore Tenant Security Proxy to tenants' KMS/logging infrastructures.

This project is still in very early stages, but will eventually implement everything that is in our [Java](https://github.com/ironcorelabs/tenant-security-client-java) and [Node](https://github.com/IronCoreLabs/tenant-security-client-nodejs/) sdks.

## Getting started

A good place to start is the [TenantSecurityClient](https://ironcorelabs.github.io/tenant-security-client-php/classes/IronCore-TenantSecurityClient.html) class, which is what the consumer should always interact with. You can also go check out the [examples](https://github.com/IronCoreLabs/tenant-security-client-php/tree/main/examples).

If you're looking for more examples the usage is very similar to that shown in our [Java Examples](https://github.com/IronCoreLabs/tenant-security-client-java/tree/main/examples).

## Documentation

We generate documentation for this library using PHPDoc and publish it to https://ironcorelabs.github.io/tenant-security-client-php/.

## Design Choices

### Error handling

Functions which can error may throw [TenantSecurityException](https://ironcorelabs.github.io/tenant-security-client-php/classes/IronCore-Exception-TenantSecurityException.html) to indicate that they've failed.

### Http and Aes library choices

This library uses CURL for http requests and OpenSSL for AES encryption/decryption.

Copyright (c) 2021 IronCore Labs, Inc. All rights reserved.
