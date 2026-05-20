<?php
/* linkwrap - a very small link shortener.
 *
 * Two routes: POST /new makes a code, GET /r/<code> sends you on. The
 * store is a tab-separated file because anything more is a dependency. */

const ALPHABET = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';

function store() { return getenv('LW_STORE') ?: __DIR__ . '/../links.tsv'; }

function code($n = 5)
{
    $out = '';
    for ($i = 0; $i < $n; $i++) $out .= ALPHABET[random_int(0, strlen(ALPHABET) - 1)];
    return $out;
}

function lookup($c)
{
    foreach (@file(store(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        [$k, $v] = array_pad(explode("\t", $line, 2), 2, '');
        if ($k === $c) return $v;
    }
    return null;
}

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === '/new') {
    $url = trim($_POST['url'] ?? '');
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        http_response_code(400);
        exit("not a url\n");
    }
    $c = code();
    file_put_contents(store(), $c . "\t" . $url . "\n", FILE_APPEND | LOCK_EX);
    $base = getenv('LW_BASE') ?: 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    exit($base . '/r/' . $c . "\n");
}

if (preg_match('#^/r/([A-Za-z0-9]{1,12})$#', $path, $m)) {
    $url = lookup($m[1]);
    if ($url === null) { http_response_code(404); exit("no such link\n"); }
    header('Location: ' . $url, true, 302);
    exit;
}

http_response_code(404);
echo "linkwrap\n";
