Project
====

## composer.json

```json
{
  "require": {
    "atphp/atsilex": "0.3.x-dev",
    "silex\/silex": "2.0.x-dev",
    "bernard\/bernard": "1.0.*@dev"
  },
  "scripts": { "post-install-cmd": ["atsilex\\module\\system\\commands\\InstallerScript::execute"] },
  "extra": {
    "atsilex": {
      "%site_name%": "My Project",
      "%site_version%": "0.1",
      "%site_url%": "http://www.v3k.net/",
      "%site_frontpage%": "hello",
      "%site_ga_code%": "UA-1234567-890",
      "%vendor_name%": "First Last"
    }
  }
}
```
