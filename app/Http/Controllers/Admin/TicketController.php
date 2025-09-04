<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function show(string $ticketId)
    {
        $ticket = Ticket::with(['journey.train', 'passenger'])
            ->where('TicketID', $ticketId)
            ->first();

        if (!$ticket) {
            return response()->json(['success' => false, 'message' => 'Ticket not found'], 404);
        }

        $journey = $ticket->journey;
        $passenger = $ticket->passenger;
        $train = $journey?->train;

        $data = [
            'ticketId' => $ticket->TicketID,
            'status' => strtolower((string) $ticket->Status),
            'passenger' => [
                'id' => $passenger?->PassengerID,
                'name' => $passenger?->Name,
            ],
            'journey' => [
                'id' => $journey?->JourneyID,
                'from' => $journey?->FromLocation,
                'to' => $journey?->ToLocation,
                'departure' => $journey?->DepartureTime,
                'arrival' => $journey?->ArrivalTime,
            ],
            'train' => [
                'id' => $train?->TrainID,
                'no' => $train?->TrainNo,
                'service' => $train?->TrainService,
            ],
        ];

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function checkIn(Request $request, string $ticketId)
    {
        return $this->updateStatus($ticketId, 'checkin');
    }

    public function checkOut(Request $request, string $ticketId)
    {
        return $this->updateStatus($ticketId, 'checkout');
    }

    private function updateStatus(string $ticketId, string $newStatus)
    {
        $ticket = Ticket::where('TicketID', $ticketId)->first();
        if (!$ticket) {
            return response()->json(['success' => false, 'message' => 'Ticket not found'], 404);
        }

        $allowed = ['pending', 'checkin', 'checkout', 'paid']; // 'paid' treated as 'pending' for backward-compat
        if (!in_array($newStatus, $allowed, true)) {
            return response()->json(['success' => false, 'message' => 'Invalid status'], 422);
        }

        $current = strtolower((string) $ticket->Status);
        $current = $current === 'paid' ? 'pending' : $current;

        // enforce transitions: pending -> checkin -> checkout
        $ok = false;
        if ($newStatus === 'checkin' && $current === 'pending') $ok = true;
        if ($newStatus === 'checkout' && $current === 'checkin') $ok = true;
        if (!$ok) {
            return response()->json(['success' => false, 'message' => 'Invalid status transition'], 422);
        }

        DB::transaction(function () use ($ticket, $newStatus) {
            $ticket->Status = $newStatus;
            $ticket->save();
        });

        // Log admin action (best-effort)
        try {
            app(\App\Services\AdminActivityLogger::class)->log(
                $newStatus === 'checkin' ? 'ticket_checkin' : 'ticket_checkout',
                ['ticket_id' => $ticket->TicketID]
            );
        } catch (\Throwable $e) {}

        return response()->json(['success' => true, 'message' => 'Status updated', 'data' => [
            'ticketId' => $ticket->TicketID,
            'status' => strtolower((string) $ticket->Status),
        ]]);
    }
}



