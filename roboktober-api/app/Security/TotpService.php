<?php

declare(strict_types=1);

namespace App\Security;

use InvalidArgumentException;

class TotpService
{
    private const BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    public function generateSecret(int $length = 32): string
    {
        if ($length < 16) {
            throw new InvalidArgumentException('Secret length must be at least 16 characters.');
        }

        $alphabet = self::BASE32_ALPHABET;
        $maxIndex = strlen($alphabet) - 1;
        $secret = '';

        for ($index = 0; $index < $length; $index++) {
            $secret .= $alphabet[random_int(0, $maxIndex)];
        }

        return $secret;
    }

    public function verifyCode(string $secret, string $code, int $window = 1, int $period = 30, int $digits = 6): bool
    {
        if (! preg_match('/^[0-9]{6}$/', $code)) {
            return false;
        }

        $currentCounter = intdiv(time(), $period);

        for ($offset = -$window; $offset <= $window; $offset++) {
            if (hash_equals($this->generateCodeForCounter($secret, $currentCounter + $offset, $digits), $code)) {
                return true;
            }
        }

        return false;
    }

    public function currentCode(string $secret, int $period = 30, int $digits = 6): string
    {
        return $this->generateCodeForCounter($secret, intdiv(time(), $period), $digits);
    }

    public function otpauthUrl(string $issuer, string $accountName, string $secret, int $digits = 6, int $period = 30): string
    {
        $label = rawurlencode($issuer.':'.$accountName);

        return 'otpauth://totp/'.$label
            .'?secret='.rawurlencode($secret)
            .'&issuer='.rawurlencode($issuer)
            .'&algorithm=SHA1'
            .'&digits='.$digits
            .'&period='.$period;
    }

    private function generateCodeForCounter(string $secret, int $counter, int $digits): string
    {
        $binarySecret = $this->base32Decode($secret);
        $binaryCounter = pack('N*', 0).pack('N*', $counter);

        $hash = hash_hmac('sha1', $binaryCounter, $binarySecret, true);
        $offset = ord($hash[19]) & 0x0F;

        $truncatedHash = ((ord($hash[$offset]) & 0x7F) << 24)
            | ((ord($hash[$offset + 1]) & 0xFF) << 16)
            | ((ord($hash[$offset + 2]) & 0xFF) << 8)
            | (ord($hash[$offset + 3]) & 0xFF);

        $pin = $truncatedHash % (10 ** $digits);

        return str_pad((string) $pin, $digits, '0', STR_PAD_LEFT);
    }

    private function base32Decode(string $value): string
    {
        $clean = strtoupper(preg_replace('/[^A-Z2-7]/', '', $value) ?? '');

        if ($clean === '') {
            throw new InvalidArgumentException('Invalid base32 input.');
        }

        $bits = '';

        foreach (str_split($clean) as $character) {
            $position = strpos(self::BASE32_ALPHABET, $character);

            if (! is_int($position)) {
                throw new InvalidArgumentException('Invalid base32 character encountered.');
            }

            $bits .= str_pad(decbin($position), 5, '0', STR_PAD_LEFT);
        }

        $bytes = '';

        for ($index = 0; $index + 8 <= strlen($bits); $index += 8) {
            $byte = (int) bindec(substr($bits, $index, 8));
            /** @var int<0, 255> $byte */
            $byte = max(0, min(255, $byte));
            $bytes .= chr($byte);
        }

        return $bytes;
    }
}
