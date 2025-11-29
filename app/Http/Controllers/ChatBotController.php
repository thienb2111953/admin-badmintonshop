<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ChatBotController extends Controller
{
    public function reply(Request $r)
    {
        $message = $r->input('message');

        $res = Http::post("http://127.0.0.1:8001/api/chatbot/search", [
            "message" => $message
        ]);

        if ($res->failed()) {
            return response()->json([
                "error" => "FastAPI error",
                "detail" => $res->body()
            ], 500);
        }

        return $res->json();
    }
}
