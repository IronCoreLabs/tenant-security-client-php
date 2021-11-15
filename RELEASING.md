# Releasing

To release a new version we need to push a tag and packagist will take care of the rest.

```bash
git checkout main
git pull
git tag <VERSION_NUMBER> (should be X.Y.Z)
git push --tags
```

Now you can head over to [packagist](https://packagist.org/packages/ironcorelabs/tenant-security-client-php) and see that your version has been released.
