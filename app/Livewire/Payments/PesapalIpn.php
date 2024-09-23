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

                if ($data['status'] == 200) {
                    $token = $data['token'];

                    // Ensure you receive the OrderTrackingId in the request payload
                    if ($request->has('OrderTrackingId')) {
                        $orderTrackingId = $request->OrderTrackingId;
                    } else {
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

                    if ($orderStatusResponse->getStatusCode() === 200) {
                        $orderStatusData = json_decode($orderStatusResponse->getBody(), true);

                        if ($orderStatusData['status'] == 200) {
                            // Fetch the transaction
                            $this->transaction = PaymentTransaction::where('order_tracking_id', $orderTrackingId)->first();

                            // Check if the transaction is already marked as completed
                            if ($this->transaction && $this->transaction->status !== 'completed') {
                                // Update the transaction details
                                $this->transaction->update([
                                    'status' => 'completed',
                                    'payment_method' => $orderStatusData['payment_method'],
                                    'amount' => $orderStatusData['amount'],
                                    'currency' => $orderStatusData['currency'],
                                    'confirmation_code' => $orderStatusData['confirmation_code'],
                                    'paid_at' => $orderStatusData['created_date'],
                                    'payment_account' => $orderStatusData['payment_account'],
                                ]);

                                // Optional: fetch voucher and attach to the transaction
                                $voucher = $this->transaction->package->vouchers()->where('is_sold', false)->inRandomOrder()->first();
                                if ($voucher) {
                                    $this->transaction->update(['voucher_id' => $voucher->id]);
                                    $voucher->update(['is_sold' => true]);
                                }

                                session()->flash('success', 'Payment Successful. Thank you for your patronage.');
                                session()->flash('success', 'Your Voucher code: ' . ($voucher ? $voucher->name : 'N/A'));
                            } else {
                                session()->flash('success', 'Transaction already completed.');
                            }
                        } else {
                            session()->flash('error', 'Payment failed or is still processing.');
                        }
                    } else {
                        session()->flash('error', 'Failed to fetch order status from Pesapal.');
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

    public function render()
    {
        return view('livewire.payments.pesapal-ipn');
    }
}
