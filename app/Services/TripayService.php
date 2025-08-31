<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Facades\Auth;

class TripayService
{
    public function getPaymentsChannels()
    {

        $apiKey = config('tripay.api_key');

        $payload = ['code' => 'BRIVA'];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_URL            => 'https://tripay.co.id/api-sandbox/merchant/payment-channel',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer DEV-p33MbvkmPaJS4jMxBqgZJe4Vp5ebm9cgNa1hioNK'],
            CURLOPT_FAILONERROR    => false
        ));

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        // echo empty($error) ? $response : $error;
        $response = json_decode($response)->data;
        return $response ?: $error;
    }
    // public function requestTransaction($method, $book)
    // {
    //     $apiKey       = config('tripay.api_key');
    //     $privateKey   = config('tripay.private_key');
    //     $merchantCode = config('tripay.merchant_code');
    //     $merchantRef  = 'px-' . time();

    //     $user = Auth::user();
    //     $book = Book::find($book->id);
    //     $amount = $book->price;

    //     $data = [
    //         'method'         => $method,
    //         'merchant_ref'   => $merchantRef,
    //         'amount'         => $amount,
    //         'customer_name'  => $user->name,
    //         'customer_email' => $user->email,
    //         // 'customer_phone' => '081234567890',
    //         'order_items'    => [
    //             [
    //                 'name'        => $book->title,
    //                 'price'       => $book->price,
    //                 'quantity'    => 1,
    //             ],
    //         ],
    //         'expired_time' => (time() + (24 * 60 * 60)), // 24 jam
    //         'signature'    => hash_hmac('sha256', $merchantCode . $merchantRef . $amount, $privateKey)
    //     ];

    //     $curl = curl_init();

    //     curl_setopt_array($curl, [
    //         CURLOPT_FRESH_CONNECT  => true,
    //         CURLOPT_URL            => 'https://tripay.co.id/api-sandbox/transaction/create',
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_HEADER         => false,
    //         CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $apiKey],
    //         CURLOPT_FAILONERROR    => false,
    //         CURLOPT_POST           => true,
    //         CURLOPT_POSTFIELDS     => http_build_query($data)
    //     ]);

    //     $response = curl_exec($curl);
    //     $error = curl_error($curl);

    //     curl_close($curl);
    //     $response = json_decode($response)->data;
    //     return $response ?: $error;
    // }
    // public function detailTranscation($reference)
    // {

    //     $apiKey = config('tripay.api_key');

    //     $payload = ['reference'    => $reference];

    //     $curl = curl_init();

    //     curl_setopt_array($curl, [
    //         CURLOPT_FRESH_CONNECT  => true,
    //         CURLOPT_URL            => 'https://tripay.co.id/api-sandbox/transaction/detail?' . http_build_query($payload),
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_HEADER         => false,
    //         CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $apiKey],
    //         CURLOPT_FAILONERROR    => false,
    //     ]);

    //     $response = curl_exec($curl);
    //     $error = curl_error($curl);

    //     curl_close($curl);
    //     $response = json_decode($response)->data;
    //     return $response ?: $error;
    // }
}
