<?php

namespace App\Services;

class TwoFactorService
{
    /**
     * Generate a random Base32 secret for TOTP
     */
    public function generateSecret(int $length = 32): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }
        return $secret;
    }

    /**
     * Build otpauth URL
     */
    public function getOtpAuthUrl(string $issuer, string $accountName, string $secret): string
    {
        $label = rawurlencode($issuer . ':' . $accountName);
        $issuerParam = rawurlencode($issuer);
        return "otpauth://totp/{$label}?secret={$secret}&issuer={$issuerParam}&algorithm=SHA1&digits=6&period=30";
    }

    /**
     * Verify a 6-digit TOTP code against a Base32 secret with optional window (in time steps)
     */
    public function verifyCode(string $base32Secret, string $code, int $window = 1): bool
    {
        $code = trim($code);
        if (!preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        $secret = $this->base32Decode($base32Secret);
        $time = floor(time() / 30);
        for ($i = -$window; $i <= $window; $i++) {
            $totp = $this->generateTotp($secret, $time + $i);
            if (hash_equals($totp, $code)) {
                return true;
            }
        }
        return false;
    }

    private function generateTotp(string $key, int $timeStep): string
    {
        $binaryTime = pack('N*', 0) . pack('N*', $timeStep);
        $hash = hash_hmac('sha1', $binaryTime, $key, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $truncatedHash = substr($hash, $offset, 4);
        $value = unpack('N', $truncatedHash)[1] & 0x7FFFFFFF;
        $mod = $value % 1000000;
        return str_pad((string) $mod, 6, '0', STR_PAD_LEFT);
    }

    private function base32Decode(string $b32): string
    {
        $b32 = strtoupper($b32);
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $flipped = array_flip(str_split($alphabet));
        $buffer = 0; $bitsLeft = 0; $result = '';
        foreach (str_split($b32) as $char) {
            if (!isset($flipped[$char])) { continue; }
            $buffer = ($buffer << 5) | $flipped[$char];
            $bitsLeft += 5;
            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $result .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }
        return $result;
    }
}



