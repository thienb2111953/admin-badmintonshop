<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatBotController extends Controller
{
    public function reply(Request $r)
    {
        $question = escapeshellarg($r->input('message'));
        $cmd = "py " . storage_path('python/chatbot-groq.py') . " {$question}";
        $output = shell_exec($cmd);
        return response()->json(['reply' => trim($output)]);
    }

}
