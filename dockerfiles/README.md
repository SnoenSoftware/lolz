# PHP runtime

The images use PHP 8.5 and Symfony 7.4 LTS. Crunz 3 replaces the old
`lavary/crunz` package. Feed HTML is parsed with Symfony DomCrawler rather
than the unmaintained PHPHtmlParser. The unused Siler, Sensio extra bundle,
and Swiftmailer dependencies have been removed.

Doctrine ORM 2.20 and DBAL 3 are intentional: existing `video_sources`
values use PHP serialization, not JSON. Keep this mapping until an explicit
data migration is provided before moving to DBAL 4.

Run the same offline, deprecation-strict checks as CI:

```sh
docker build . -f dockerfiles/Dockerfile --target=cron -t lolz:test
docker run --rm --network none --entrypoint php -w /app lolz:test -d error_reporting=-1 vendor/bin/phpunit --do-not-cache-result
```

Tests cover parser behavior, existing serialized data, migrations, feed
regeneration, HTTP/GraphQL responses, development startup, and Crunz task
execution. They use disposable SQLite databases and mocked feed responses;
they do not access production data or external feed services.
