<?php

namespace App\Services;

class WhatsAppService
{
    protected string $waApiUrl;
    protected string $waApiKey;
    protected string $waApiSecret;


    public function __construct()
    {
        $this->waApiUrl =
            rtrim(
                (string) env('WHATSAPP_GATEWAY_URL'),
                '/'
            );

        $this->waApiKey =
            (string) env('WHATSAPP_API_KEY');

        $this->waApiSecret =
            (string) env('WHATSAPP_HMAC_SECRET');
    }


    /*
     * =========================================================
     * GENERATE HMAC SIGNATURE
     * =========================================================
     *
     * HARUS SAMA DENGAN NODE.JS
     *
     * Format:
     *
     * timestamp.phone.message
     *
     * Contoh:
     *
     * 1755840000.6288210841268.Halo
     *
     * Untuk check-number:
     *
     * 1755840000.6288210841268.
     *
     */

    // private function generateSignature(
    //     string $timestamp,
    //     ?string $phone = null,
    //     ?string $message = null
    // ): string {

    //     $payload =
    //         $timestamp .
    //         '.' .
    //         ($phone ?? '') .
    //         '.' .
    //         ($message ?? '');

    //     return hash_hmac(
    //         'sha256',
    //         $payload,
    //         $this->waApiSecret
    //     );
    // }

    private function generateSignature(
        string $timestamp,
        ?string $phone = null,
        ?string $message = null,
        ?string $body = null
    ): string {

        /*
        * BULK
        *
        * Signature:
        *
        * timestamp.sha256(body)
        */
        if ($body !== null) {

            $bodyHash = hash(
                'sha256',
                $body
            );

            $payload =
                $timestamp .
                '.' .
                $bodyHash;

        } else {

            /*
            * SINGLE
            *
            * Signature:
            *
            * timestamp.phone.message
            */

            $payload =
                $timestamp .
                '.' .
                ($phone ?? '') .
                '.' .
                ($message ?? '');
        }

        return hash_hmac(
            'sha256',
            $payload,
            $this->waApiSecret
        );
    }


    /*
     * =========================================================
     * HMAC HEADERS
     * =========================================================
     */

    // private function getHmacHeaders(
    //     string $timestamp,
    //     ?string $phone = null,
    //     ?string $message = null
    // ): array {

    //     $signature =
    //         $this->generateSignature(
    //             $timestamp,
    //             $phone,
    //             $message
    //         );

    //     return [

    //         'Content-Type' =>
    //             'application/json',

    //         'Accept' =>
    //             'application/json',

    //         'X-API-Key' =>
    //             $this->waApiKey,

    //         'X-Timestamp' =>
    //             $timestamp,

    //         'X-Signature' =>
    //             $signature,

    //     ];
    // }

    private function getHmacHeaders(
        string $timestamp,
        ?string $phone = null,
        ?string $message = null,
        ?string $body = null
    ): array {

        $signature =
            $this->generateSignature(
                $timestamp,
                $phone,
                $message,
                $body
            );

        return [

            'Content-Type' =>
                'application/json',

            'Accept' =>
                'application/json',

            'X-API-Key' =>
                $this->waApiKey,

            'X-Timestamp' =>
                $timestamp,

            'X-Signature' =>
                $signature,

        ];
    }


    /*
     * =========================================================
     * SEND WHATSAPP
     * =========================================================
     */

    public function send(
        ?string $phone = null,
        ?string $message = null,
        ?string $groupId = null,
        string $messageType = 'text',
        ?string $mediaType = null,
        ?string $mediaUrl = null,
        ?string $filename = null
    ): array {

        /*
         * =====================================================
         * VALIDASI DESTINATION
         * =====================================================
         */

        if (
            empty($phone) &&
            empty($groupId)
        ) {

            throw new \InvalidArgumentException(
                'phone atau group_id wajib diisi.'
            );
        }


        if (
            !empty($phone) &&
            !empty($groupId)
        ) {

            throw new \InvalidArgumentException(
                'phone dan group_id tidak boleh digunakan bersamaan.'
            );
        }


        /*
         * =====================================================
         * VALIDASI TEXT
         * =====================================================
         */

        if (
            $messageType === 'text' &&
            (
                $message === null ||
                trim($message) === ''
            )
        ) {

            throw new \InvalidArgumentException(
                'Pesan tidak boleh kosong.'
            );
        }


        /*
         * =====================================================
         * VALIDASI MEDIA
         * =====================================================
         */

        if (
            $messageType !== 'text' &&
            empty($mediaUrl)
        ) {

            throw new \InvalidArgumentException(
                'media_url wajib diisi untuk pesan media.'
            );
        }


        /*
         * =====================================================
         * PAYLOAD
         * =====================================================
         */

        $payload = [

            'phone' =>
                $phone,

            'group_id' =>
                $groupId,

            'message' =>
                $message,

            'message_type' =>
                $messageType,

            'media_type' =>
                $mediaType,

            'media_url' =>
                $mediaUrl,

            'filename' =>
                $filename,

        ];


        /*
         * Buang NULL
         */

        $payload =
            array_filter(
                $payload,
                static function ($value) {

                    return $value !== null;

                }
            );


        /*
         * =====================================================
         * JSON
         * =====================================================
         */

        $body =
            json_encode(
                $payload,
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES
            );


        if ($body === false) {

            throw new \RuntimeException(
                'Gagal membuat JSON request WhatsApp.'
            );
        }


        /*
         * =====================================================
         * PATH
         * =====================================================
         */

        $path =
            '/api/whatsapp/send-message';


        /*
         * =====================================================
         * TIMESTAMP
         * =====================================================
         *
         * Timestamp dibuat SATU KALI.
         */

        $timestamp =
            (string) time();


        /*
         * =====================================================
         * HMAC
         * =====================================================
         *
         * Untuk personal:
         *
         * timestamp.phone.message
         *
         * Untuk group:
         *
         * timestamp..message
         *
         */

        $headers =
            $this->getHmacHeaders(
                $timestamp,
                $phone,
                $message
            );


        /*
         * =====================================================
         * CURL
         * =====================================================
         */

        $client =
            service(
                'curlrequest',
                [

                    'timeout' =>
                        30,

                    'connect_timeout' =>
                        10,

                    'http_errors' =>
                        false,

                ]
            );


        $response =
            $client->post(
                $this->waApiUrl . $path,
                [

                    'headers' =>
                        $headers,

                    'body' =>
                        $body,

                ]
            );


        /*
         * =====================================================
         * RESPONSE
         * =====================================================
         */

        $statusCode =
            $response->getStatusCode();

        $responseBody =
            (string)
            $response->getBody();

        $result =
            json_decode(
                $responseBody,
                true
            );


        if (!is_array($result)) {

            $result = [

                'success' =>
                    false,

                'message' =>
                    $responseBody,

            ];
        }


        /*
         * =====================================================
         * ERROR
         * =====================================================
         */

        if (
            $statusCode < 200 ||
            $statusCode >= 300
        ) {

            throw new \RuntimeException(

                $result['message']
                    ??
                'Gagal mengirim pesan WhatsApp.',

                $statusCode

            );
        }


        return $result;
    }


    /*
     * =========================================================
     * CHECK NUMBER
     * =========================================================
     */

    public function checkNumber(
        string $phone
    ): array {

        /*
         * =====================================================
         * NORMALISASI NOMOR
         * =====================================================
         */

        $phone =
            trim($phone);


        $phone =
            preg_replace(
                '/[^0-9]/',
                '',
                $phone
            );


        if ($phone === '') {

            throw new \InvalidArgumentException(
                'Nomor WhatsApp wajib diisi.'
            );
        }


        /*
         * 08xxx
         * menjadi
         * 628xxx
         */

        if (
            str_starts_with(
                $phone,
                '0'
            )
        ) {

            $phone =
                '62' .
                substr(
                    $phone,
                    1
                );
        }


        /*
         * =====================================================
         * PATH
         * =====================================================
         */

        $path =
            '/api/whatsapp/check-number';


        /*
         * =====================================================
         * PAYLOAD
         * =====================================================
         */

        $payload = [

            'phone' =>
                $phone,

        ];


        $body =
            json_encode(
                $payload,
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES
            );


        if ($body === false) {

            throw new \RuntimeException(
                'Gagal membuat JSON request WhatsApp.'
            );
        }


        /*
         * =====================================================
         * TIMESTAMP
         * =====================================================
         */

        $timestamp =
            (string) time();


        /*
         * =====================================================
         * HMAC
         * =====================================================
         *
         * SAMA PERSIS DENGAN POSTMAN
         *
         * Postman:
         *
         * const payload =
         * `${timestamp}.${phone}.${message}`;
         *
         * Untuk check-number:
         *
         * message = ''
         *
         * Jadi:
         *
         * timestamp.phone.
         *
         */

        $headers =
            $this->getHmacHeaders(
                $timestamp,
                $phone,
                ''
            );


        /*
         * =====================================================
         * CURL
         * =====================================================
         */

        $client =
            service(
                'curlrequest',
                [

                    'timeout' =>
                        15,

                    'connect_timeout' =>
                        5,

                    'http_errors' =>
                        false,

                ]
            );


        /*
         * =====================================================
         * POST
         * =====================================================
         */

        $response =
            $client->post(
                $this->waApiUrl . $path,
                [

                    'headers' =>
                        $headers,

                    'body' =>
                        $body,

                ]
            );


        /*
         * =====================================================
         * RESPONSE
         * =====================================================
         */

        $statusCode =
            $response->getStatusCode();

        $responseBody =
            (string)
            $response->getBody();

        $result =
            json_decode(
                $responseBody,
                true
            );


        if (!is_array($result)) {

            $result = [

                'success' =>
                    false,

                'message' =>
                    $responseBody,

            ];
        }


        /*
         * =====================================================
         * ERROR
         * =====================================================
         */

        if (
            $statusCode < 200 ||
            $statusCode >= 300
        ) {

            throw new \RuntimeException(

                $result['message']
                    ??
                'Gagal mengecek nomor WhatsApp.',

                $statusCode

            );
        }


        return $result;
    }

    // private function getBulkHmacHeaders(
    //     string $timestamp,
    //     string $body
    // ): array {

    //     $signature =
    //         $this->generateBulkSignature(
    //             $timestamp,
    //             $body
    //         );

    //     return [

    //         'Content-Type' =>
    //             'application/json',

    //         'Accept' =>
    //             'application/json',

    //         'X-API-Key' =>
    //             $this->waApiKey,

    //         'X-Timestamp' =>
    //             $timestamp,

    //         'X-Signature' =>
    //             $signature,

    //     ];
    // }

    // private function generateBulkSignature(
    //     string $timestamp,
    //     string $body
    // ): string {

    //     $bodyHash =
    //         hash(
    //             'sha256',
    //             $body
    //         );

    //     $payload =
    //         $timestamp .
    //         '.' .
    //         $bodyHash;

    //     return hash_hmac(
    //         'sha256',
    //         $payload,
    //         $this->waApiSecret
    //     );
    // }

    /*
    * =========================================================
    * BULK INSERT WHATSAPP QUEUE
    * =========================================================
    *
    * Mengirim banyak pesan sekaligus ke Node.js.
    *
    * Support:
    *
    * - phone
    * - group_id
    * - text
    * - image
    * - video
    * - document
    * - audio
    *
    * Contoh:
    *
    * [
    *     [
    *         'phone' => '628123456789',
    *         'message' => 'Halo 1',
    *         'message_type' => 'text'
    *     ],
    *
    *     [
    *         'group_id' => '120363xxxxxxxx@g.us',
    *         'message' => 'Halo grup',
    *         'message_type' => 'text'
    *     ],
    *
    *     [
    *         'phone' => '628987654321',
    *         'message' => 'Dokumen',
    *         'message_type' => 'document',
    *         'media_type' => 'application/pdf',
    *         'media_url' => 'https://example.com/file.pdf',
    *         'filename' => 'file.pdf'
    *     ]
    * ]
    *
    */

    public function bulkInsert(
        array $jobs
    ): array {

        /*
        * =====================================================
        * VALIDASI ARRAY
        * =====================================================
        */

        if (
            empty($jobs)
        ) {

            throw new \InvalidArgumentException(
                'Data queue tidak boleh kosong.'
            );

        }


        /*
        * =====================================================
        * NORMALISASI DATA
        * =====================================================
        *
        * Tidak melakukan normalisasi nomor.
        * Nomor ditangani oleh Node.js.
        */

        $payloadJobs = [];


        foreach (
            $jobs as $index => $job
        ) {

            if (
                !is_array($job)
            ) {

                throw new \InvalidArgumentException(
                    "Data queue pada index {$index} tidak valid."
                );

            }


            $data = [

                'phone' =>
                    $job['phone'] ?? null,

                'group_id' =>
                    $job['group_id']
                    ?? $job['groupId']
                    ?? null,

                'message' =>
                    $job['message'] ?? null,

                'message_type' =>
                    $job['message_type']
                    ?? 'text',

                'media_type' =>
                    $job['media_type']
                    ?? null,

                'media_url' =>
                    $job['media_url']
                    ?? null,

                'filename' =>
                    $job['filename']
                    ?? null,

            ];


            /*
            * Buang NULL agar JSON lebih bersih.
            */

            $data =
                array_filter(
                    $data,
                    static function ($value) {

                        return $value !== null;

                    }
                );


            $payloadJobs[] =
                $data;

        }


        /*
        * =====================================================
        * PAYLOAD
        * =====================================================
        */

        $payload = [

            'jobs' =>
                $payloadJobs,

        ];


        /*
        * =====================================================
        * JSON
        * =====================================================
        */

        $body =
            json_encode(
                $payload,
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES
            );


        if (
            $body === false
        ) {

            throw new \RuntimeException(
                'Gagal membuat JSON bulk WhatsApp.'
            );

        }


        /*
        * =====================================================
        * TIMESTAMP
        * =====================================================
        */

        $timestamp =
            (string) time();


        /*
        * =====================================================
        * HMAC
        * =====================================================
        *
        * Bulk:
        *
        * SHA256(JSON body)
        *
        * kemudian:
        *
        * timestamp.bodyHash
        *
        */

        $headers =
            $this->getHmacHeaders(
                timestamp: $timestamp,
                phone: '',
                message: ''
            );


        /*
        * =====================================================
        * PATH
        * =====================================================
        */

        $path =
            '/api/whatsapp/queue/bulk';


        /*
        * =====================================================
        * CURL
        * =====================================================
        */

        $client =
            service(
                'curlrequest',
                [

                    'timeout' =>
                        30,

                    'connect_timeout' =>
                        10,

                    'http_errors' =>
                        false,

                ]
            );


        /*
        * =====================================================
        * POST
        * =====================================================
        */

        $response =
            $client->post(
                $this->waApiUrl . $path,
                [

                    'headers' =>
                        $headers,

                    'body' =>
                        $body,

                ]
            );


        /*
        * =====================================================
        * RESPONSE
        * =====================================================
        */

        $statusCode =
            $response->getStatusCode();


        $responseBody =
            (string)
            $response->getBody();


        $result =
            json_decode(
                $responseBody,
                true
            );


        /*
        * Jika Node tidak mengembalikan JSON.
        */

        if (
            !is_array($result)
        ) {

            $result = [

                'success' =>
                    false,

                'message' =>
                    $responseBody,

            ];

        }


        /*
        * =====================================================
        * ERROR
        * =====================================================
        */

        if (
            $statusCode < 200 ||
            $statusCode >= 300
        ) {

            throw new \RuntimeException(

                $result['message']
                    ??
                'Gagal memasukkan bulk WhatsApp ke queue.',

                $statusCode

            );

        }


        return $result;

    }
}