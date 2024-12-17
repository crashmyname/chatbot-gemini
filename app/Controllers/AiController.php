<?php

namespace App\Controllers;

use App\Models\Categories;
use Support\BaseController;
use Support\Http;
use Support\Request;
use Support\Response;
use Support\Validator;
use Support\View;
use Support\CSRFToken;

class AiController extends BaseController
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY', 'default_key');
        $this->baseUrl = env('GEMINI_BASE_URL', 'default_url');
    }

    public function chat(Request $request)
    {
        // Ambil input pertanyaan dari request
        $question = $request->question;

        // Validasi pertanyaan tidak boleh kosong
        if (empty($question)) {
            $errorMessage = 'Pertanyaan tidak boleh kosong.';

            // Jika AJAX, kembalikan JSON
            if (Request::isAjax()) {
                return Response::json(['error' => $errorMessage], 400);
            }

            return View::render('gemini', ['error' => $errorMessage]);
        }

        try {
            // Kirim permintaan ke Gemini API
            $response = $this->callGeminiApi($question);

            // Jika request melalui AJAX
            if ($request->isAjax()) {
                return Response::json(['response' => $response], 200);
            }

            // Jika bukan AJAX, render ke view
            return View::render('gemini', ['response' => $response]);

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            // Jika AJAX, kembalikan JSON
            if ($request->isAjax()) {
                return Response::json(['error' => $errorMessage], 500);
            }

            return View::render('gemini', ['error' => $errorMessage]);
        }
    }

    private function callGeminiApi($question)
    {
        try {
            $url = $this->baseUrl . '?key=' . $this->apiKey;

            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $question]
                        ]
                    ]
                ]
            ];

            // Gunakan helper Http::post untuk mengirim permintaan POST
            $response = Http::post($url, $payload);

            // Periksa status respons dan proses data jika sukses
            if ($response['status'] === 200) {
                $content = $response['response']['candidates'][0]['content']['parts'];
                $texts = array_map(function ($part) {
                    return $part['text'];
                }, $content);

                return $texts; // Return only the texts from parts
            }

            // Jika ada error, kembalikan pesan kesalahan
            throw new \Exception('Error in API response: ' . json_encode($response), $response['status']);
        } catch (\Exception $e) {
            throw new \Exception('Gagal mengambil respons dari Gemini API: ' . $e->getMessage(), $e->getCode());
        }
    }
}
