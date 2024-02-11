<?php
// webhook.php
//
// The library needs to be configured with your account's secret key.
// Ensure the key is kept out of any version control system you might be using.
$stripe = new \Stripe\StripeClient('sk_test_...');

// This is your Stripe CLI webhook secret for testing your endpoint locally.
$endpoint_secret = $config->stripe_signing_secret;

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload,
        $sig_header,
        $endpoint_secret
    );
} catch (\UnexpectedValueException $e) {
    // Invalid payload
    http_response_code(400);
    exit();
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    // Invalid signature
    http_response_code(400);
    exit();
}

// Handle the event
switch ($event->type) {
    case 'checkout.session.completed':
        $paymentIntent = $event->data->object;

        $database->update('subscriptions', [
            'customer_id' => $paymentIntent->customer,
            'plan' => 13
        ], [
            'selector' => $paymentIntent->client_reference_id,
        ]);
        break;
    case 'customer.subscription.deleted':
        $subscription = $event->data->object;
        $database->update('subscriptions', [
            'plan' => 10
        ], [
            'customer_id' => $subscription->customer,
        ]);
        break;
    case 'customer.subscription.updated':
        $subscription = $event->data->object;
        if ($subscription->cancel_at_period_end) {
            $database->update('subscriptions', [
                'text' => 'This subscription will be canceled at ' . date('Y-m-d', $subscription->current_period_end),
                'expires' => $subscription->current_period_end,
            ], [
                'customer_id' => $subscription->customer,
            ]);
        } else {
            $database->update('subscriptions', [
                'text' => '',
            ], [
                'customer_id' => $subscription->customer,
            ]);
        }
        break;
    default:
        echo 'Received unknown event type ' . $event->type;
}

http_response_code(200);
