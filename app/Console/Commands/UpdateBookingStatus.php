<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\Journey;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateBookingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-booking-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update booking status to Completed one day after the journey arrival time for OneWay or return journey arrival time for Return bookings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Get current date (without time)
            $today = Carbon::today();

            // Find bookings with status 'Booked' or 'Pending' and load related journeys
            $bookings = Booking::whereIn('Status', ['Booked', 'Pending'])
                ->with(['journey', 'journey2'])
                ->get();

            foreach ($bookings as $booking) {
                $arrivalTime = null;

                if ($booking->BookingType === 'OneWay' && $booking->journey) {
                    // For OneWay bookings, use Journey.ArrivalTime
                    $arrivalTime = $booking->journey->ArrivalTime
                        ? Carbon::parse($booking->journey->ArrivalTime)->startOfDay()
                        : null;
                } elseif ($booking->BookingType === 'Return') {
                    // For Return bookings, use Journey2.ArrivalTime
                    $arrivalTime = $booking->journey2->ArrivalTime
                        ? Carbon::parse($booking->journey2->ArrivalTime)->startOfDay()
                        : null;
                }

                if ($arrivalTime) {
                    // Check if today is one day or more after arrival time
                    if ($today->greaterThanOrEqualTo($arrivalTime->copy()->addDay())) {
                        $oldStatus = $booking->Status;

                        // Update status based on current status
                        if ($booking->Status === 'Booked') {
                            $booking->Status = 'Completed';
                        } elseif ($booking->Status === 'Pending') {
                            $booking->Status = 'Cancelled';
                        }

                        // Only save if status changed
                        if ($booking->Status !== $oldStatus) {
                            $booking->save();

                            Log::info('Booking status updated', [
                                'BookingID'   => $booking->BookingID,
                                'BookingType' => $booking->BookingType,
                                'JourneyID'   => $booking->JourneyID,
                                'JourneyID2'  => $booking->JourneyID2 ?? null,
                                'ArrivalTime' => $arrivalTime->format('Y-m-d H:i:s'),
                                'OldStatus'   => $oldStatus,
                                'NewStatus'   => $booking->Status,
                            ]);
                        }
                    } else {
                        Log::debug('Booking not updated', [
                            'BookingID'   => $booking->BookingID,
                            'BookingType' => $booking->BookingType,
                            'JourneyID'   => $booking->JourneyID,
                            'JourneyID2'  => $booking->JourneyID2 ?? null,
                            'ArrivalTime' => $arrivalTime->format('Y-m-d H:i:s'),
                            'Reason'      => 'Today is not ≥ 1 day after arrival time',
                        ]);
                    }
                } else {
                    Log::debug('Booking skipped due to missing arrival time', [
                        'BookingID'   => $booking->BookingID,
                        'BookingType' => $booking->BookingType,
                        'JourneyID'   => $booking->JourneyID,
                        'JourneyID2'  => $booking->JourneyID2 ?? null,
                    ]);
                }
            }

            $this->info('Booking statuses updated successfully.');
            return 0;
        } catch (\Exception $e) {
            Log::error('Error updating booking statuses: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
            ]);
            $this->error('Failed to update booking statuses: ' . $e->getMessage());
            return 1;
        }
    }


}