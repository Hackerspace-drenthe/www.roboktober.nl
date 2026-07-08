<?php

declare(strict_types=1);

/**
 * GitHub webhook endpoint for automated deployments.
 *
 * Expected server-side config (example in deploy/github-webhook.env.example):
 * - GITHUB_WEBHOOK_SECRET
 * - GITHUB_DEPLOY_BRANCH
 * - GITHUB_DEPLOY_SERVICE
 *
 * The service is started through sudo, so configure a restricted sudoers rule
 * for the webserver user (www-data).
 */

const CONFIG_FILE_CANDIDATES = [
    '/etc/roboktober/github-webhook.env',
    '/etc/default/roboktober-github-webhook',
];

/** @return array<string, string> */
function loadKeyValueFile(string $path): array
{
    if (!is_readable($path)) {
        return [];
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return [];
    }

    $result = [];

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) {
            continue;
        }

        $key = trim($parts[0]);
        $value = trim($parts[1]);
        $value = trim($value, "\"'");

        if ($key !== '') {
            $result[$key] = $value;
        }
    }

    return $result;
}

/** @return array<string, string> */
function loadWebhookConfig(): array
{
    $fileValues = [];

    foreach (CONFIG_FILE_CANDIDATES as $candidate) {
        $fileValues = array_merge($fileValues, loadKeyValueFile($candidate));
    }

    return [
        'secret' => ($fileValues['GITHUB_WEBHOOK_SECRET'] ?? '') ?: (getenv('GITHUB_WEBHOOK_SECRET') ?: ''),
        'branch' => ($fileValues['GITHUB_DEPLOY_BRANCH'] ?? '') ?: (getenv('GITHUB_DEPLOY_BRANCH') ?: 'master'),
        'service' => ($fileValues['GITHUB_DEPLOY_SERVICE'] ?? '') ?: (getenv('GITHUB_DEPLOY_SERVICE') ?: 'roboktober-deploy.service'),
    ];
}

function respond(int $status, array $body): never
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($body, JSON_UNESCAPED_SLASHES);
    exit;
}

function getHeaderValue(string $name): string
{
    $serverKey = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
    $value = $_SERVER[$serverKey] ?? '';
    return is_string($value) ? $value : '';
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    respond(405, ['ok' => false, 'error' => 'Method not allowed']);
}

$config = loadWebhookConfig();
$secret = $config['secret'];
$branch = $config['branch'];
$service = $config['service'];

if ($secret === '') {
    error_log('github-deploy-hook: missing GITHUB_WEBHOOK_SECRET');
    respond(500, ['ok' => false, 'error' => 'Webhook secret is not configured']);
}

$rawPayload = file_get_contents('php://input');
if (!is_string($rawPayload) || $rawPayload === '') {
    respond(400, ['ok' => false, 'error' => 'Missing payload']);
}

$signatureHeader = getHeaderValue('X-Hub-Signature-256');
if (!str_starts_with($signatureHeader, 'sha256=')) {
    respond(401, ['ok' => false, 'error' => 'Missing signature']);
}

$incomingSignature = substr($signatureHeader, 7);
$expectedSignature = hash_hmac('sha256', $rawPayload, $secret);

if (!hash_equals($expectedSignature, $incomingSignature)) {
    respond(401, ['ok' => false, 'error' => 'Invalid signature']);
}

$event = getHeaderValue('X-GitHub-Event');

if ($event === 'ping') {
    respond(200, ['ok' => true, 'message' => 'pong']);
}

if ($event !== 'push') {
    respond(202, ['ok' => true, 'message' => 'Ignored event', 'event' => $event]);
}

$payload = json_decode($rawPayload, true);
if (!is_array($payload)) {
    respond(400, ['ok' => false, 'error' => 'Invalid JSON payload']);
}

$ref = $payload['ref'] ?? '';
$expectedRef = 'refs/heads/' . $branch;

if (!is_string($ref) || $ref !== $expectedRef) {
    respond(202, [
        'ok' => true,
        'message' => 'Ignored branch',
        'expected' => $expectedRef,
        'received' => is_string($ref) ? $ref : 'unknown',
    ]);
}

$command = sprintf('sudo /bin/systemctl start %s 2>&1', escapeshellarg($service));
$output = [];
$exitCode = 0;
exec($command, $output, $exitCode);

if ($exitCode !== 0) {
    error_log('github-deploy-hook: failed to start service: ' . implode("\n", $output));
    respond(500, ['ok' => false, 'error' => 'Failed to start deploy service']);
}

respond(202, [
    'ok' => true,
    'message' => 'Deploy triggered',
    'service' => $service,
    'branch' => $branch,
]);
