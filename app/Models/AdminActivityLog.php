<?php
/**
 * author: Lau Aik Hung
 * student id: 23WMR14555
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminActivityLog extends Model
{
    use HasFactory;

    protected $table = 'admin_activity_logs';

    public $timestamps = true;

    protected $fillable = [
        'admin_email',
        'action',
        'details',
    ];

    /**
     * Encrypt details on write; decrypt on read.
     * - Storage format keeps DB column type as JSON by wrapping ciphertext.
     * - Backward-compatible with legacy plaintext JSON arrays/objects.
     */
    protected $casts = [
        // Intentionally not casting 'details' to array here; handled via accessors
    ];

    /**
     * Decrypt the details attribute when retrieving from DB.
     * Returns array|string depending on original payload.
     */
    public function getDetailsAttribute($value)
    {
        try {
            // Normalize into array if possible
            $normalized = null;
            if (is_array($value)) {
                $normalized = $value;
            } elseif (is_string($value)) {
                $first = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    // If first decode yields a JSON string, decode again
                    if (is_string($first) && (str_starts_with($first, '{') || str_starts_with($first, '['))) {
                        $second = json_decode($first, true);
                        $normalized = json_last_error() === JSON_ERROR_NONE ? $second : $first;
                    } else {
                        $normalized = $first;
                    }
                } else {
                    $normalized = $value; // raw string
                }
            }

            // Encrypted payload path
            if (is_array($normalized) && array_key_exists('payload', $normalized)) {
                $plain = \Illuminate\Support\Facades\Crypt::decryptString((string) $normalized['payload']);
                $maybeArray = json_decode($plain, true);
                return json_last_error() === JSON_ERROR_NONE ? $maybeArray : $plain;
            }

            // Legacy plaintext JSON (array/object) or raw string
            return is_array($normalized) ? $normalized : $value;
        } catch (\Throwable $e) {
            // On failure, fall back to best-effort decode
            return is_string($value) ? (json_decode($value, true) ?? $value) : $value;
        }
    }

    /**
     * Encrypt the details attribute before saving to DB.
     * Accepts array|object|string; stores JSON with a ciphertext payload.
     */
    public function setDetailsAttribute($value): void
    {
        try {
            if (is_array($value) || is_object($value)) {
                $cipher = \Illuminate\Support\Facades\Crypt::encryptString(json_encode($value));
                // Store as JSON object to avoid double-encoded JSON strings in JSON column
                $this->attributes['details'] = json_encode(['payload' => $cipher], JSON_UNESCAPED_SLASHES);
                return;
            }
            if (is_string($value)) {
                $cipher = \Illuminate\Support\Facades\Crypt::encryptString($value);
                $this->attributes['details'] = json_encode(['payload' => $cipher], JSON_UNESCAPED_SLASHES);
                return;
            }
            // Fallback for scalars/null
            $cipher = \Illuminate\Support\Facades\Crypt::encryptString(json_encode($value));
            $this->attributes['details'] = json_encode(['payload' => $cipher], JSON_UNESCAPED_SLASHES);
        } catch (\Throwable $e) {
            // Never block logging: store plaintext JSON as last resort
            $this->attributes['details'] = is_string($value) ? $value : json_encode($value);
        }
    }
}


