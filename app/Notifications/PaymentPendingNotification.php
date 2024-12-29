<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Twilio\Rest\Client;

class PaymentPendingNotification extends Notification
{
    use Queueable;

    protected $student;

    public function __construct($student)
    {
        $this->student = $student;
    }

    // Send the notification via SMS (Twilio)
    public function via($notifiable)
    {
        return ['database', 'sms']; // You can also add other channels if needed.
    }

    // SMS Notification
    public function toSms($notifiable)
    {
        $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));

        $message = "Dear {$this->student->name}, your payment status is pending. Please make the payment by the due date.";

        $twilio->messages->create(
            $this->student->mobile_no,  // The student's mobile number
            [
                'from' => env('TWILIO_PHONE_NUMBER'),
                'body' => $message
            ]
        );
    }

    // Store the notification in the database (optional)
    public function toDatabase($notifiable)
    {
        return [
            'message' => "Your payment status is pending. Please make the payment.",
        ];
    }
}
