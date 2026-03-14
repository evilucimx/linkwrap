# linkwrap

A very small self-hosted link shortener. One file, no database, no
dependencies beyond PHP 8. I got tired of pasting hundred-character URLs
into chat.

## Why

Every shortener I looked at either wanted a database, wanted an account, or
rewrote the link through their own analytics. This one keeps a flat file and
does nothing else.

## Install

```
git clone https://github.com/REPLACE-ME/linkwrap
cd linkwrap
php -S 0.0.0.0:8080 -t src src/index.php
```

The router argument matters - `/r/<code>` is not a file on disk.

Point a web server at `src/` for anything real, with everything rewritten
to `index.php`.

## Use

```
curl -d 'url=https://example.com/a/very/long/path' http://localhost:8080/new
-> http://localhost:8080/r/x7Qa
```

Codes are four characters from a 58-character alphabet, which is about
eleven million before you need to care.

## Config

| Variable | Default | What |
|---|---|---|
| `LW_STORE` | `./links.tsv` | Where the mapping lives |
| `LW_BASE` | request host | Base URL printed for new links |

## Licence

MIT.
