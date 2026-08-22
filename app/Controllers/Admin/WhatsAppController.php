<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class WhatsAppController extends BaseController
{
    private string $waApiUrl;
    private string $waApiKey;
    private string $waApiSecret;

    public function __construct()
    {
        $this->waApiUrl = rtrim(
            env('WHATSAPP_GATEWAY_URL'),
            '/'
        );

        $this->waApiKey = env('WHATSAPP_API_KEY');

        /*
         * Secret untuk HMAC.
         *
         * HARUS sama dengan secret yang digunakan
         * oleh hmacMiddleware di Node.js.
         */
        $this->waApiSecret = env('WHATSAPP_HMAC_SECRET');
    }


    /*
     * =========================================================
     * INDEX
     * =========================================================
     */

    public function index()
    {
        return view('admin/whatsapp/v_index', [
            'title' => 'WhatsApp Gateway'
        ]);
    }


    /*
     * =========================================================
     * HMAC SIGNATURE
     * =========================================================
     */

    private function generateSignature(
        string $method,
        string $path,
        string $timestamp,
        string $body = ''
    ): string {

        /*
         * FORMAT SIGNATURE
         *
         * PERHATIAN:
         * Format ini harus sama persis dengan
         * hmacMiddleware Node.js.
         */

        $payload =
            $timestamp .
            strtoupper($method) .
            $path .
            $body;


        return hash_hmac(
            'sha256',
            $payload,
            $this->waApiSecret
        );
    }


    /*
     * =========================================================
     * HEADER HMAC
     * =========================================================
     */

    private function getHmacHeaders(
        string $phone = '',
        string $message = ''
    ): array
    {
        $timestamp = (string) time();

        $payload =
            $timestamp .
            '.' .
            $phone .
            '.' .
            $message;

        $signature = hash_hmac(
            'sha256',
            $payload,
            env('WHATSAPP_HMAC_SECRET')
        );

        return [
            'X-API-Key' =>
                env('WHATSAPP_API_KEY'),

            'X-Timestamp' =>
                $timestamp,

            'X-Signature' =>
                $signature,
        ];
    }


    /*
     * =========================================================
     * STATUS
     * =========================================================
     */

    public function status()
    {
        try {

            $client =
                service('curlrequest', [
                    'timeout' => 10,
                    'connect_timeout' => 5,
                ]);


            $path =
                '/api/whatsapp/status';


            $response =
                $client->get(
                    $this->waApiUrl . $path,
                    [
                        'headers' => [
                            'x-api-key' =>
                                $this->waApiKey,

                            'Accept' =>
                                'application/json',
                        ],
                    ]
                );


            $body =
                json_decode(
                    $response->getBody(),
                    true
                );


            return $this->response->setJSON(
                $body
            );


        } catch (\Throwable $e) {

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                        'Gagal mengambil status WhatsApp.',
                    'error' =>
                        $e->getMessage()
                ]);
        }
    }


    /*
     * =========================================================
     * QR
     * =========================================================
     */

    public function qr()
    {
        try {

            $client =
                service('curlrequest', [
                    'timeout' => 10,
                    'connect_timeout' => 5,
                ]);


            $path =
                '/api/whatsapp/qr';


            $response =
                $client->get(
                    $this->waApiUrl . $path,
                    [
                        'headers' => [
                            'x-api-key' =>
                                $this->waApiKey,

                            'Accept' =>
                                'application/json',
                        ],
                    ]
                );


            $body =
                json_decode(
                    $response->getBody(),
                    true
                );


            return $this->response->setJSON(
                $body
            );


        } catch (\Throwable $e) {

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                        'QR code tidak tersedia.'
                ]);
        }
    }


    /*
     * =========================================================
     * LOGOUT
     * =========================================================
     */

    public function logout()
    {
        try {

            $client =
                service('curlrequest', [
                    'timeout' => 15,
                    'connect_timeout' => 5,
                ]);


            $path =
                '/api/whatsapp/logout';


            $response =
                $client->post(
                    $this->waApiUrl . $path,
                    [
                        'headers' => [
                            'x-api-key' =>
                                $this->waApiKey,

                            'Accept' =>
                                'application/json',
                        ],
                    ]
                );


            $body =
                json_decode(
                    $response->getBody(),
                    true
                );


            return $this->response->setJSON(
                $body
            );


        } catch (\Throwable $e) {

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                        'Gagal logout WhatsApp.',
                    'error' =>
                        $e->getMessage()
                ]);
        }
    }


    /*
     * =========================================================
     * GET GROUPS
     * =========================================================
     *
     * GET /api/whatsapp/groups
     *
     * Digunakan oleh controller lain maupun view.
     *
     */

    public function group()
    {
        try {

            $result = $this->getGroups();

            return $this->response
                ->setStatusCode(200)
                ->setJSON($result);

        } catch (\Throwable $e) {

            return $this->response
                ->setStatusCode(
                    $e->getCode() >= 400 &&
                    $e->getCode() < 600
                        ? $e->getCode()
                        : 500
                )
                ->setJSON([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
        }
    }


    /*
    * =========================================================
    * REUSABLE: GET GROUPS
    * =========================================================
    */
    public function getGroups(): array
    {
        $client = service('curlrequest', [
            'timeout' => 15,
            'connect_timeout' => 5,
        ]);

        $path = '/api/whatsapp/groups';

        /*
        * Gateway membutuhkan body JSON,
        * walaupun request-nya GET.
        */
        $body = '{}';

        /*
        * Signature:
        *
        * timestamp..
        *
        * karena:
        *
        * phone   = ""
        * message = ""
        */
        $headers = $this->getHmacHeaders();

        $headers['Content-Type'] =
            'application/json';

        $headers['Accept'] =
            'application/json';

        $response = $client->get(
            $this->waApiUrl . $path,
            [
                'headers' => $headers,
                'body' => $body,
                'http_errors' => false,
            ]
        );

        $statusCode =
            $response->getStatusCode();

        $responseBody =
            $response->getBody();

        $data =
            json_decode(
                $responseBody,
                true
            );

        if (!is_array($data)) {

            throw new \RuntimeException(
                'Response WhatsApp Gateway tidak valid.',
                500
            );
        }

        if (
            $statusCode < 200 ||
            $statusCode >= 300
        ) {

            throw new \RuntimeException(
                $data['message']
                    ?? 'Gagal mengambil group WhatsApp.',
                $statusCode
            );
        }

        return $data;
    }


    /*
     * =========================================================
     * REUSABLE: CHECK NUMBER
     * =========================================================
     *
     * Contoh:
     *
     * $result =
     *     $this->checkNumber('081234567890');
     *
     */

    public function checkNumber(
        string $phone
    ): array {

        $client =
            service('curlrequest', [
                'timeout' => 15,
                'connect_timeout' => 5,
            ]);


        $path =
            '/api/whatsapp/check-number';


        /*
         * Endpoint Gateway Anda saat ini
         * menggunakan GET.
         *
         * Karena nomor ada di query string,
         * signature harus memperhitungkan query
         * sesuai implementasi hmacMiddleware Node.
         */

        $query =
            http_build_query([
                'phone' => $phone
            ]);


        $fullPath =
            $path . '?' . $query;


        $headers =
            $this->getHmacHeaders(
                'GET',
                $fullPath
            );


        $response =
            $client->get(
                $this->waApiUrl . $fullPath,
                [
                    'headers' =>
                        $headers,

                    'http_errors' =>
                        false,
                ]
            );


        $statusCode =
            $response->getStatusCode();


        $body =
            json_decode(
                $response->getBody(),
                true
            );


        if (
            $statusCode < 200 ||
            $statusCode >= 300
        ) {

            /*
             * 404 nomor tidak terdaftar
             * tetap dikembalikan sebagai hasil
             * validasi nomor.
             */

            if (
                $statusCode === 404 &&
                isset($body['data'])
            ) {

                return $body;
            }


            throw new \RuntimeException(
                $body['message']
                    ?? 'Gagal mengecek nomor WhatsApp.',
                $statusCode
            );
        }


        return $body;
    }


    /*
     * =========================================================
     * REUSABLE: SEND MESSAGE
     * =========================================================
     *
     * Contoh:
     *
     * $result =
     *     $this->sendMessage(
     *         '081234567890',
     *         'Halo'
     *     );
     *
     */

    public function sendMessage(
    ?string $phone = null,
    ?string $message = null,
        ?string $groupId = null,
        string $messageType = 'text',
        ?string $mediaType = null,
        ?string $mediaUrl = null,
        ?string $filename = null
    ): array {

        /*
        * ==========================================
        * VALIDASI DESTINATION
        * ==========================================
        */

        $phone = $phone !== null
            ? trim($phone)
            : null;

        $groupId = $groupId !== null
            ? trim($groupId)
            : null;


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
        * ==========================================
        * MESSAGE TYPE
        * ==========================================
        */

        $messageType =
            strtolower(
                trim(
                    $messageType ?: 'text'
                )
            );


        $allowedTypes = [
            'text',
            'image',
            'video',
            'document',
            'audio',
        ];


        if (
            !in_array(
                $messageType,
                $allowedTypes,
                true
            )
        ) {

            throw new \InvalidArgumentException(
                'message_type tidak valid. ' .
                'Gunakan: text, image, video, document, atau audio.'
            );

        }


        /*
        * ==========================================
        * TEXT
        * ==========================================
        */

        $message =
            $message !== null
                ? (string) $message
                : '';


        if (
            $messageType === 'text' &&
            trim($message) === ''
        ) {

            throw new \InvalidArgumentException(
                'Pesan WhatsApp tidak boleh kosong.'
            );

        }


        /*
        * ==========================================
        * MEDIA
        * ==========================================
        */

        $mediaType =
            $mediaType !== null
                ? strtolower(
                    trim($mediaType)
                )
                : null;


        $mediaUrl =
            $mediaUrl !== null
                ? trim($mediaUrl)
                : null;


        $filename =
            $filename !== null
                ? trim($filename)
                : null;


        /*
        * Pesan non-text wajib mempunyai
        * media_url.
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
        * ==========================================
        * ENDPOINT NODE JS
        * ==========================================
        */

        $path =
            '/api/whatsapp/send-message';


        /*
        * ==========================================
        * PAYLOAD
        * ==========================================
        *
        * Payload ini mengikuti struktur queue
        * Node.js terbaru.
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
        * ==========================================
        * JSON
        * ==========================================
        */

        $body =
            json_encode(
                $payload,
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES |
                JSON_THROW_ON_ERROR
            );


        /*
        * ==========================================
        * HMAC
        * ==========================================
        */

        $headers =
            $this->getHmacHeaders(
                'POST',
                $path,
                $body
            );


        /*
        * ==========================================
        * CURL
        * ==========================================
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


        try {

            $response =
                $client->post(
                    $this->waApiUrl . $path,
                    [

                        'headers' =>
                            array_merge(
                                $headers,
                                [
                                    'Content-Type' =>
                                        'application/json',

                                    'Accept' =>
                                        'application/json',
                                ]
                            ),

                        'body' =>
                            $body,

                        'http_errors' =>
                            false,

                    ]
                );


        } catch (
            \Throwable $e
        ) {

            throw new \RuntimeException(
                'Tidak dapat terhubung ke WhatsApp Gateway: ' .
                $e->getMessage(),
                0,
                $e
            );

        }


        /*
        * ==========================================
        * RESPONSE
        * ==========================================
        */

        $statusCode =
            $response->getStatusCode();


        $responseBody =
            (string)
            $response->getBody();


        /*
        * JSON RESPONSE
        */

        try {

            $result =
                json_decode(
                    $responseBody,
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                );

        } catch (
            \JsonException $e
        ) {

            throw new \RuntimeException(
                'Response WhatsApp Gateway bukan JSON valid. ' .
                'HTTP ' .
                $statusCode .
                ': ' .
                $responseBody
            );

        }


        /*
        * ==========================================
        * HTTP ERROR
        * ==========================================
        */

        if (
            $statusCode < 200 ||
            $statusCode >= 300
        ) {

            throw new \RuntimeException(

                $result['message']
                    ?? $result['error']
                    ?? (
                        'Gagal mengirim pesan WhatsApp. ' .
                        'HTTP ' .
                        $statusCode
                    ),

                $statusCode

            );

        }


        /*
        * ==========================================
        * NODE JS RESPONSE ERROR
        * ==========================================
        *
        * Walaupun HTTP 2xx, tetap cek success.
        */

        if (
            isset($result['success']) &&
            $result['success'] === false
        ) {

            throw new \RuntimeException(

                $result['message']
                    ?? $result['error']
                    ?? 'WhatsApp Gateway gagal memproses pesan.',

                $statusCode

            );

        }


        /*
        * ==========================================
        * SUCCESS
        * ==========================================
        */

        return $result;

    }
}