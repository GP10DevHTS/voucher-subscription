<?php

namespace App\Livewire\Clients;

use App\Models\Package;
use App\Models\PaymentTransaction;
use Livewire\Attributes\Layout;
use Livewire\Component;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
// use Laravel\Jetstream\InteractsWithBanner;

#[Layout('layouts.guest')]
class WelcomePage extends Component
{
    // use InteractsWithBanner;

    public $pagesect = 1;
    public $package;

    public $name;
    public $email;
    public $phone;


    public function render()
    {
        return view('livewire.clients.welcome-page', [
            'packages' => Package::all(),
        ]);
    }

    public function selectPackage($uuid)
    {
        $this->package = $uuid;
        $this->pagesect = 2;
    }

    public function createClient()
    {
        // Validate input
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required_if:phone,null|email|max:255',
            'phone' => 'required|string|max:15',
        ]);

        $package = Package::where('slug', $this->package)->firstOrFail();

        $customer = PaymentTransaction::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'package_id' => $package->id,
        ]);

        $consumerKey = env('PESAPAL_CONSUMER_KEY'); // Add your consumer key to .env
        $consumerSecret = env('PESAPAL_CONSUMER_SECRET'); // Add your consumer secret to .env

        $client = new Client();

        try {
            $response = $client->post('https://pay.pesapal.com/v3/api/Auth/RequestToken', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'consumer_key' => $consumerKey,
                    'consumer_secret' => $consumerSecret,
                ],
            ]);

            // Check if the response is successful
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);

                if ($data['status'] == 200) {
                    $token = $data['token'];

                    // Proceed to register the IPN
                    $ipnResponse = $client->post('https://pay.pesapal.com/v3/api/URLSetup/RegisterIPN', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $token,
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json',
                        ],
                        'json' => [
                            'url' => route('voucher-payment'), // The route for your IPN notification
                            'ipn_notification_type' => 'POST', // or 'GET', depending on your setup
                        ],
                    ]);

                    if ($ipnResponse->getStatusCode() === 200) {
                        session()->flash('success', 'IPN registration successful. Proceeding to payment...');
                        // Redirect or handle success
                        $ipnData = json_decode($ipnResponse->getBody(), true);
                        // save the ipn_id

                        dd($ipnData);

                        $customer->ipn_id = $ipnData['ipn_id'];
                        $customer->save();
                        // press the order (https://pay.pesapal.com/v3/api/Transactions/SubmitOrderRequest)
                        $pressOrder = $client->post('https://pay.pesapal.com/v3/api/Transactions/SubmitOrderRequest', [
                            'headers' => [
                                'Authorization' => 'Bearer ' . $token,
                                'Accept' => 'application/json',
                                'Content-Type' => 'application/json',
                            ],
                            'json' => [
                                'id' => $customer->uuid,
                                'currency' => $package->currency,
                                'amount' => $package->price,
                                'description' => 'voucher payment ' . $package->name,
                                'callback_url' => route('voucher-payment'),
                                'notification_id' => $ipnData['ipn_id'],
                                'billing_address' => [
                                    'phone_number' => $customer->phone ?? null,
                                    'email' => $customer->email ?? null,
                                    'first_name' =>  $customer->name,
                                ],
                            ],
                        ]); // =============================================

                        if ($pressOrder->getStatusCode() === 200) {
                            // redirect here
                            $orderData = json_decode($pressOrder->getBody(), true);
                            $customer->order_tracking_id = $orderData['order_tracking_id'];
                            $customer->save();

                            dd( $orderData)










                        } else {
                            session()->flash('error', 'Failed to register payment order with Pesapal.');
                        }
                    } else {
                        session()->flash('error', 'Failed to register IPN with Pesapal.');
                    }
                } else {
                    session()->flash('error', 'Failed to authenticate with Pesapal.');
                }
            } else {
                session()->flash('error', 'Failed to authenticate with Pesapal.');
            }
        } catch (RequestException $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }
}
