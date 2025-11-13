<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserAddressController extends Controller
{
    public function index(Request $request)
    {
        $data = DB::table('dia_chi_nguoi_dung')
            ->where('id_nguoi_dung', Auth::guard('api')->id())
            ->orderBy('mac_dinh', 'desc')
            ->get();

        return Response::Success($data, 'Get User Address Successfully');
    }

    public function store(Request $request)
    {
        $ten_nguoi_dung = $request->input('ten_nguoi_dung');
        $so_dien_thoai = $request->input('so_dien_thoai');
        $dia_chi = $request->input('dia_chi');
        $email = $request->input('email');
        $mac_dinh = $request->input('mac_dinh');

        $result = DB::table('dia_chi_nguoi_dung')
            ->insert([
                'id_nguoi_dung' => Auth::guard('api')->id(),
                'ten_nguoi_dung' => $ten_nguoi_dung,
                'dia_chi' => $dia_chi,
                'email' => $email,
                'so_dien_thoai' => $so_dien_thoai,
                'mac_dinh' => $mac_dinh,
            ]);

        if ($result) {
            return Response::Success('', 'Add User Address Successfully');
        }

        return Response::Error('', 'Add User Address Failed');
    }

    public function edit(Request $request, $id)
    {
        $userId = Auth::guard('api')->id();
        if (!$userId) {
            return Response::Error('', 'Unauthorized', 401);
        }

        $ten_nguoi_dung = $request->input('ten_nguoi_dung');
        $so_dien_thoai = $request->input('so_dien_thoai');
        $dia_chi = $request->input('dia_chi');
        $email = $request->input('email');

        $mac_dinh = filter_var($request->input('mac_dinh'), FILTER_VALIDATE_BOOLEAN);

        try {
            DB::transaction(function () use ($id, $userId, $ten_nguoi_dung, $so_dien_thoai, $dia_chi, $email, $mac_dinh) {

                if ($mac_dinh) {
                    DB::table('dia_chi_nguoi_dung')
                        ->where('id_nguoi_dung', $userId)
                        ->where('id_dia_chi_nguoi_dung', '!=', $id)
                        ->update(['mac_dinh' => 0]);
                }

                DB::table('dia_chi_nguoi_dung')
                    ->where('id_dia_chi_nguoi_dung', $id)
                    ->where('id_nguoi_dung', $userId)
                    ->update([
                        'ten_nguoi_dung' => $ten_nguoi_dung,
                        'dia_chi' => $dia_chi,
                        'email' => $email,
                        'so_dien_thoai' => $so_dien_thoai,
                        'mac_dinh' => $mac_dinh ? 1 : 0,
                    ]);
            });

        } catch (\Exception $e) {
            return Response::Error('', 'Update User Address Failed');
        }

        return Response::Success('', 'Update User Address Successfully');
    }

    public function destroy(Request $request, $id)
    {
        $result = DB::table('dia_chi_nguoi_dung')->where('id_dia_chi_nguoi_dung', $id)->delete();
        if ($result) {
            return Response::Success('', 'Delete User Address Successfully');
        }

        return Response::Error('', 'Delete User Address Failed');
    }

    public function getDefaultAddress()
    {
        $data = DB::table('dia_chi_nguoi_dung')
            ->where('id_nguoi_dung', Auth::guard('api')->id())
            ->where('mac_dinh', true)
            ->first();
        return Response::Success($data, 'Get User Address Successfully');
    }
}
