<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use GuzzleHttp\Client;
// use GuzzleHttp\Exception\RequestException;
// use App\Models\PaymentTransaction;


// class PesapalIpnServer extends Controller
// {
//     public function index(Request $request){
//         $consumerKey = env('PESAPAL_CONSUMER_KEY'); // Add your consumer key to .env
//         $consumerSecret = env('PESAPAL_CONSUMER_SECRET'); // Add your consumer secret to .env

//         $client = new Client();

//         try {
//             // First, authenticate to get the token
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

//             // Check if the authentication response is successful
//             if ($response->getStatusCode() === 200) {
//                 $data = json_decode($response->getBody(), true);

//                 // Debug the authentication response data
//                 // dd($data); // exists

//                 if ($data['status'] == 200) {
//                     $token = $data['token'];

//                     // Ensure you receive the OrderTrackingId in the request payload
//                     if ($request->has('OrderTrackingId')) {
//                         $orderTrackingId = $request->OrderTrackingId;
//                     } else {
//                         // Debug in case OrderTrackingId is missing
//                         dd('OrderTrackingId is missing in the request.');
//                     }

//                     // Use the token to get the order status
//                     $orderStatusResponse = $client->get('https://pay.pesapal.com/v3/api/Transactions/GetTransactionStatus', [
//                         'headers' => [
//                             'Authorization' => 'Bearer ' . $token,
//                             'Content-Type' => 'application/json',
//                             'Accept' => 'application/json',
//                         ],
//                         'query' => [
//                             'orderTrackingId' => $orderTrackingId,
//                         ],
//                     ]);

//                     // Debug the order status response
//                     // dd($orderStatusResponse);

//                     if ($orderStatusResponse->getStatusCode() === 200) {
//                         $orderStatusData = json_decode($orderStatusResponse->getBody(), true);

//                         // Debug the final order status data
//                         // dd($orderStatusData);

//                         if ($orderStatusData['status'] == 200) {
                           
//                             $transaction = PaymentTransaction::where('order_tracking_id', $request->OrderTrackingId)->firstOrFail();

//                             // get a random voucher with the same package as the transaction
//                             $voucher = $transaction->package->vouchers()->inRandomOrder()->first();

//                             $transaction->update([
//                                 'status' => 'completed',
//                                 'ipn_id' => $orderStatusData['payment_method'],
//                                 'amount' => $orderStatusData['amount'],
//                                 'currency' => $orderStatusData['currency'],
//                                 'confirmation_code' => $orderStatusData['confirmation_code'],
//                                 'paid_at' => $orderStatusData['created_date'],
//                                 'payment_account' => $orderStatusData['payment_account'],
//                                 'voucher_id' => $voucher->id,
//                             ]);

//                             $voucher->update([
//                                 'is_sold' => true
//                             ]);
                            
                           
//                             session()->flash('success', 'Payment Successful. Thank you for your patronage.');
//                             session()->flash('success', 'Voucher code: ' . $voucher->name);

                            

//                         } else {
//                             session()->flash('error', 'Payment failed or is still processing.');
//                         }
//                     } else {
//                         session()->flash('error', 'Failed to fetch order status from Pesapal. 3');
//                     }
//                 } else {
//                     session()->flash('error', 'Failed to authenticate with Pesapal. 2');
//                 }
//             } else {
//                 session()->flash('error', 'Failed to authenticate with Pesapal. 1');
//             }
//         } catch (RequestException $e) {
//             // Handle and debug exceptions properly
//             session()->flash('error', 'Error: ' . $e->getMessage());
//             // dd($e->getMessage());
//         }
//     }
// }


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Models\PaymentTransaction;

class PesapalIpnServer extends Controller
{
    public function index(Request $request)
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

            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);

                if ($data['status'] == 200) {
                    $token = $data['token'];

                    if ($request->has('OrderTrackingId')) {
                        $orderTrackingId = $request->OrderTrackingId;
                    } else {
                        return response()->json([
                            'status' => 500,
                            'message' => 'OrderTrackingId is missing in the request.'
                        ]);
                    }

                    // Get the order status from Pesapal
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
                            // Fetch the transaction from the DB
                            $transaction = PaymentTransaction::where('order_tracking_id', $orderTrackingId)->first();

                            // Check if the transaction is already marked as completed
                            if ($transaction && $transaction->status === 'completed') {
                                return response()->json([
                                    'status' => 200,
                                    'message' => 'Transaction already completed.',
                                ]);
                            }

                            // If not already completed, proceed with the update
                            $voucher = $transaction->package->vouchers()->where('is_sold', false)->inRandomOrder()->first();

                            if ($voucher) {
                                // Update transaction details
                                $transaction->update([
                                    'status' => 'completed',
                                    'payment_method' => $orderStatusData['payment_method'],
                                    'amount' => $orderStatusData['amount'],
                                    'currency' => $orderStatusData['currency'],
                                    'confirmation_code' => $orderStatusData['confirmation_code'],
                                    'paid_at' => $orderStatusData['created_date'],
                                    'payment_account' => $orderStatusData['payment_account'],
                                    'voucher_id' => $voucher->id,
                                ]);

                                // Mark the voucher as sold
                                $voucher->update([
                                    'is_sold' => true
                                ]);

                                // Return JSON to Pesapal confirming successful processing
                                return response()->json([
                                    'orderNotificationType' => 'IPNCHANGE',
                                    'orderTrackingId' => $orderTrackingId,
                                    'orderMerchantReference' => $transaction->merchant_reference,
                                    'status' => 200, // IPN was successfully processed
                                ]);
                            } else {
                                // Handle the case where no vouchers are available
                                return response()->json([
                                    'status' => 500,
                                    'message' => 'No available vouchers for the selected package.',
                                ]);
                            }
                        } else {
                            return response()->json([
                                'status' => 500,
                                'message' => 'Payment failed or is still processing.',
                            ]);
                        }
                    } else {
                        return response()->json([
                            'status' => 500,
                            'message' => 'Failed to fetch order status from Pesapal.',
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'Failed to authenticate with Pesapal.',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to authenticate with Pesapal.',
                ]);
            }
        } catch (RequestException $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }
}
