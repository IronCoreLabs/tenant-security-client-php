# Changelog

## v0.3.0

- Restricted visibility of some methods intended for internal use only
  - `TenantSecurityRequest` and `Aes` in particular should not be used directly.
- Encryption now throw a `CryptoException` when trying to encrypt a document that has already been IronCore encrypted.
  - If you have a use case for double-encrypting a document, please open an issue explaining and we can work on accommodating you.
- Added `encryptWithExistingKey` to `TenantSecurityClient`.

## v0.2.4

- Fix bug with null EventMetadata.timestampMillis.

## v0.2.3

- Add error code 209.

## v0.2.2

- Add method to encrypt a batch of files with a single call to the TSP.
- Add method to decrypt a batch of files with a single call to the TSP.
- Add method to log a security event to the TSP/LogDriver.

## v0.2.1

- Add method to re-key existing EDEKs

## v0.2.0

- Initial beta release of the SDK, including encrypt and decrypt methods
