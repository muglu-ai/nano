<?php

namespace App\Services;

use App\Models\Ticket\DelegateNotification;
use App\Models\Ticket\TicketDelegate;
use App\Models\Ticket\TicketContact;
use App\Mail\DelegateNotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Send notification to specific delegate
     */
    public function sendToDelegate(
        int $delegateId,
        string $title,
        string $message,
        string $type = 'info',
        ?int $createdBy = null,
        bool $sendEmail = false
    ): DelegateNotification {
        $notification = DelegateNotification::create([
            'delegate_id' => $delegateId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'created_by' => $createdBy,
            'is_read' => false,
        ]);

        if ($sendEmail) {
            $this->sendEmail($notification);
        }

        return $notification;
    }

    /**
     * Send notification to all delegates in contact's registrations
     */
    public function sendToContact(
        int $contactId,
        string $title,
        string $message,
        string $type = 'info',
        ?int $createdBy = null,
        bool $sendEmail = false
    ): array {
        $notifications = [];

        // Get all delegates for this contact
        $delegates = TicketDelegate::whereHas('registration', function ($query) use ($contactId) {
            $query->where('contact_id', $contactId);
        })->get();

        foreach ($delegates as $delegate) {
            $notification = DelegateNotification::create([
                'delegate_id' => $delegate->id,
                'contact_id' => $contactId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'created_by' => $createdBy,
                'is_read' => false,
            ]);

            $notifications[] = $notification;

            if ($sendEmail) {
                $this->sendEmail($notification);
            }
        }

        // Also create contact-level notification
        DelegateNotification::create([
            'contact_id' => $contactId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'created_by' => $createdBy,
            'is_read' => false,
        ]);

        return $notifications;
    }

    /**
     * Broadcast notification to all delegates
     */
    public function sendToAll(
        string $title,
        string $message,
        string $type = 'info',
        ?int $createdBy = null,
        bool $sendEmail = false
    ): int {
        // Get all active contacts with delegates
        $contacts = TicketContact::whereHas('registrations.delegates')->get();

        $count = 0;
        foreach ($contacts as $contact) {
            $delegates = TicketDelegate::whereHas('registration', function ($query) use ($contact) {
                $query->where('contact_id', $contact->id);
            })->pluck('id');

            foreach ($delegates as $delegateId) {
                DelegateNotification::create([
                    'delegate_id' => $delegateId,
                    'contact_id' => $contact->id,
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'created_by' => $createdBy,
                    'is_read' => false,
                ]);
                $count++;
            }

            // Create contact-level notification
            $notification = DelegateNotification::create([
                'contact_id' => $contact->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'created_by' => $createdBy,
                'is_read' => false,
            ]);

            if ($sendEmail) {
                $this->sendEmail($notification);
            }
        }

        return $count;
    }

    /**
     * Send email notification
     */
    public function sendEmail(DelegateNotification $notification): void
    {
        try {
            $contact = $notification->contact;
            $delegate = $notification->delegate;

            // Determine recipient email
            $email = $delegate?->email ?? $contact?->email;
            $name = $delegate?->full_name ?? $contact?->name;

            if (!$email) {
                Log::warning('Cannot send notification email: No email address', [
                    'notification_id' => $notification->id,
                ]);
                return;
            }

            Mail::to($email)->send(new DelegateNotificationMail($notification, $name));

            Log::info('Delegate notification email sent', [
                'notification_id' => $notification->id,
                'email' => $email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send delegate notification email', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
