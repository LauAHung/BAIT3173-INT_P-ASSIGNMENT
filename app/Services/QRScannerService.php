<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Exception;

class QRScannerService
{
    /**
     * Scan QR code for check-in/check-out
     */
    public function scanQR($qrCode, $operation = 'check-in')
    {
        try {
            // Decode QR code to get user information
            $qrData = $this->decodeQR($qrCode);
            
            if (!$qrData) {
                return [
                    'success' => false,
                    'message' => 'Invalid QR code'
                ];
            }

            $userId = $qrData['user_id'] ?? null;
            $user = User::find($userId);

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found'
                ];
            }

            // Process check-in/check-out
            $result = $this->processCheckOperation($user, $operation);

            return [
                'success' => true,
                'message' => ucfirst($operation) . ' successful',
                'data' => [
                    'user' => $user,
                    'operation' => $operation,
                    'timestamp' => now(),
                    'result' => $result
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to scan QR: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate QR code for user
     */
    public function generateQR($userId, $type = 'boarding')
    {
        try {
            $user = User::find($userId);

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found'
                ];
            }

            // Generate QR data
            $qrData = [
                'user_id' => $user->user_id,
                'user_email' => $user->email,
                'user_name' => $user->first_name . ' ' . $user->last_name,
                'type' => $type,
                'timestamp' => now()->timestamp,
                'token' => Str::random(32)
            ];

            // Encode QR data
            $qrCode = $this->encodeQR($qrData);

            return [
                'success' => true,
                'message' => 'QR code generated successfully',
                'data' => [
                    'qr_code' => $qrCode,
                    'qr_data' => $qrData,
                    'user' => $user
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to generate QR: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Decode QR code
     */
    private function decodeQR($qrCode)
    {
        try {
            // Placeholder for QR decoding
            // In real implementation, you would use a QR library
            $decoded = json_decode(base64_decode($qrCode), true);
            
            return $decoded ?: null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Encode data to QR code
     */
    private function encodeQR($data)
    {
        try {
            // Placeholder for QR encoding
            // In real implementation, you would use a QR library
            return base64_encode(json_encode($data));
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Process check-in/check-out operation
     */
    private function processCheckOperation($user, $operation)
    {
        // Placeholder for check-in/check-out logic
        $result = [
            'operation' => $operation,
            'user_id' => $user->user_id,
            'timestamp' => now(),
            'status' => 'completed'
        ];

        // Update user's last activity
        $user->last_login_at = now();
        $user->save();

        return $result;
    }

    /**
     * Validate QR code
     */
    public function validateQR($qrCode)
    {
        try {
            $qrData = $this->decodeQR($qrCode);
            
            if (!$qrData) {
                return [
                    'success' => false,
                    'message' => 'Invalid QR code format'
                ];
            }

            // Check if QR code is expired (24 hours)
            $timestamp = $qrData['timestamp'] ?? 0;
            $expiryTime = now()->subHours(24)->timestamp;
            
            if ($timestamp < $expiryTime) {
                return [
                    'success' => false,
                    'message' => 'QR code has expired'
                ];
            }

            return [
                'success' => true,
                'message' => 'QR code is valid',
                'data' => $qrData
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to validate QR: ' . $e->getMessage()
            ];
        }
    }
} 