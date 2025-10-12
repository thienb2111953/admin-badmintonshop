<?php
namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    /**
     * @param $data
     * @param $message
     * @return mixed
     */
    public static function Success($data='', $message='')
    {
        $data = ((object)[
            'data' => $data,
            'message' => $message,
            'status' => 200
        ]);
        return response()->json($data, 200);
    }

    /**
     * @param $message
     * @param $error
     * @return mixed
     */
    public static function Error($message='', $error='')
    {
        $data = ((object)[
            'message' => $message,
            'errors' => $error,
            'status' => 400
        ]);
        return response()->json($data, 200);
    }
}
