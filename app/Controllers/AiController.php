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
        $this->apiKey = env('GEMINI_API_KEY','default_key');
        $this->baseUrl = env('GEMINI_BASE_URL','default_key');
    }

    public function chat(Request $request)
    {
        // Ambil input pertanyaan dari request
        $question = $request->question;

        // Validasi pertanyaan tidak boleh kosong
        if (empty($question)) {
            return View::render('gemini', ['error' => 'Pertanyaan tidak boleh kosong.']);
        }

        try {
            // Kirim permintaan ke Gemini API
            $response = $this->callGeminiApi($question);

            // return response()->json($data);
            return View::render('gemini', ['response' => $response]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
        // Tampilkan hasil ke view
        // return View::render('gemini', ['response' => $data]);
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
                $texts = array_map(function($part) {
                    return $part['text'];
                }, $content);
    
                return $texts; // Return only the texts from parts
            }
    
            // Jika ada error, kembalikan pesan kesalahan
            throw new \Exception('Error in API response: ' . json_encode($response), $response['status']);
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => $e->getCode(),
            ];
        }
    }
}