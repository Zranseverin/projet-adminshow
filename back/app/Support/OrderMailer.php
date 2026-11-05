<?php

namespace App\Support;

use App\Mail\OrderConfirmationMail;
use App\Mail\PaymentReceiptMail;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderMailer
{
    public function sendConfirmation(Order $order): void
    {
        $this->send($order, new OrderConfirmationMail($order), 'confirmation');
    }

    public function sendReceipt(Order $order): void
    {
        if ($order->payment_status !== 'paid') {
            return;
        }

        $this->send($order, new PaymentReceiptMail($order), 'receipt');
    }

    private function send(Order $order, object $mailable, string $type): void
    {
        if (! $order->customer_email) {
            return;
        }

        try {
            Mail::to($order->customer_email)->send($mailable);
        } catch (\Throwable $exception) {
            Log::warning('Order email not sent.', [
                'type' => $type,
                'order_id' => $order->id,
                'reference' => $order->reference,
                'email' => $order->customer_email,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
