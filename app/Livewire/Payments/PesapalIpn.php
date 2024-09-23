<?php

// namespace App\Livewire\Payments;

// use Illuminate\Http\Request;
// use Livewire\Attributes\Layout;

// use GuzzleHttp\Client;
// use GuzzleHttp\Exception\RequestException;
// use Livewire\Component;



// #[Layout('layouts.guest')]
// class PesapalIpn extends Component
// {
//     public function mount(Request $request)
//     {
//         $consumerKey = env('PESAPAL_CONSUMER_KEY'); // Add your consumer key to .env
//         $consumerSecret = env('PESAPAL_CONSUMER_SECRET'); // Add your consumer secret to .env

//         $client = new Client();

//         try {
//             $response = $client->post('https://pay.pesapal.com/v3/api/Auth/RequestToken', [
//                 'headers' => [
//                     'Accept' => 'application/json',
//                     'Content-Type' => 'application/json',
//                 ],
//                 'json' => [
//                     'consumer_key' => $consumerKey,
//                     'consumer_secret' => $consumerSecret,
//                 ],
//             ]);

//             // Check if the response is successful
//             if ($response->getStatusCode() === 200) {
//                 $data = json_decode($response->getBody(), true);

//                 if ($data['status'] == 200) {
//                     $token = $data['token'];
// // dd($token);
//                     $orderStatusResponse = $client->get(' https://pay.pesapal.com/v3/api/Transactions/GetTransactionStatus',[
//                         'headers' => [
//                             'Authorization' => 'Bearer ' . $token,
//                             'Content-Type' => 'application/json',
//                             'Accept' => 'application/json',
//                         ],
//                         'json' => [
//                             'orderTrackingId' => $request->OrderTrackingId,
//                         ],
//                     ]);

//                     dd($orderStatusResponse);

//                     if($orderStatusResponse->getStatusCode() === 200) {
//                         $orderStatusData = json_decode($orderStatusResponse->getBody(), true);
                        
//                         dd($orderStatusData);
                        
//                         if ($orderStatusData['status'] == 200) {

//                             session()->flash('success', 'Payment Successful. Thank you for your patronage.');
//                         } else {
//                             session()->flash('error', 'Failed to authenticate with Pesapal. please try to reload this page.');
//                         }
//                     } else {
                        
//                         session()->flash('error', 'Failed to authenticate with Pesapal. please try to reload this page.');
//                     }



//                 } else {
//                     session()->flash('error', 'Failed to authenticate with Pesapal.');
//                 }
//             } else {
//                 session()->flash('error', 'Failed to authenticate with Pesapal.');
//             }
//         } catch (RequestException $e) {
//             session()->flash('error', 'Error: ' . $e->getMessage());
//         }
//     }
//     public function render()
//     {
//         return view('livewire.payments.pesapal-ipn');
//     }
// }

namespace App\Livewire\Payments;

use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Livewire\Attributes\Layout;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Livewire\Component;

#[Layout('layouts.guest')]
class PesapalIpn extends Component
{

    public $transaction;

    public function mount(Request $request)
    {
        $consumerKey = env('PESAPAL_CONSUMER_KEY'); // Add your consumer key to .env
        $consumerSecret = env('PESAPAL_CONSUMER_SECRET'); // Add your consumer secret to .env

        $client = new Client();

        try {
            // First, authenticate to get the token
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

            // Check if the authentication response is successful
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);

                // Debug the authentication response data
                // dd($data); // exists

                if ($data['status'] == 200) {
                    $token = $data['token'];

                    // Ensure you receive the OrderTrackingId in the request payload
                    if ($request->has('OrderTrackingId')) {
                        $orderTrackingId = $request->OrderTrackingId;
                    } else {
                        // Debug in case OrderTrackingId is missing
                        // dd('OrderTrackingId is missing in the request.');
                        session()->flash('error', 'OrderTrackingId is missing in the request.');
                        return;
                    }

                    // Use the token to get the order status
                    $orderStatusResponse = $client->get('https://pay.pesapal.com/v3/api/Transactions/GetTransactionStatus', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $token,
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                        ],
                        'query' => [
                            'orderTrackingId' => $orderTrackingId,
                        ],
                    ]);

                    // Debug the order status response
                    // dd($orderStatusResponse);

                    if ($orderStatusResponse->getStatusCode() === 200) {
                        $orderStatusData = json_decode($orderStatusResponse->getBody(), true);

                        // Debug the final order status data
                        // dd($orderStatusData);

                        if ($orderStatusData['status'] == 200) {
                           
                            $this->transaction = PaymentTransaction::where('order_tracking_id', $request->OrderTrackingId)->firstOrFail();

                                                       
                           
                            session()->flash('success', 'Payment Successful. Thank you for your patronage.');
                            // session()->flash('success', 'Payment Successful. Thank you for your patronage.\n Your Voucher code: ' . $this->transaction->voucher->name);

                            

                        } else {
                            session()->flash('error', 'Payment failed or is still processing.');
                        }
                    } else {
                        session()->flash('error', 'Failed to fetch order status from Pesapal. 3');
                    }
                } else {
                    session()->flash('error', 'Failed to authenticate with Pesapal. 2');
                }
            } else {
                session()->flash('error', 'Failed to authenticate with Pesapal. 1');
            }
        } catch (RequestException $e) {
            // Handle and debug exceptions properly
            session()->flash('error', 'Error: ' . $e->getMessage());
            // dd($e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.payments.pesapal-ipn');
    }
}
