<?php

namespace App\Http\Controllers;

use App\Gifts;
use App\Http\Resources\Gifts as ResourcesGifts;
use App\Http\Resources\GiftsCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GiftsController extends Controller
{
    public function index($limit = 10, $sortBy = 10)
    {
        try {
            return new GiftsCollection(Gifts::paginate());
        } catch (\Throwable $th) {
            Log::debug($th);
            return response()->json(['data' => [], 'message' => 'error, internal server error', 'status' => 'error']);
        }
    }
    public function show($id)
    {
        try {
            return new ResourcesGifts(Gifts::find($id));
        } catch (\Throwable $th) {
            Log::debug($th);
            return response()->json(['data' => [], 'message' => 'error, internal server error', 'status' => 'error']);
        }
    }

    public function store(Request $request)
    {
        try {
            return response()->json(['test store']);
        } catch (\Throwable $th) {
            Log::debug($th);
            return response()->json(['data' => [], 'message' => 'error, internal server error', 'status' => 'error']);
        }
    }

    public function destroy($id)
    {
        try {

            if (!Gifts::find($id)) {
                return response()->json(['data' => [], 'message' => 'failed, data gifts not found', 'status' => 'error']);
            }

            if (!Gifts::destroy($id)) {
                return response()->json(['data' => [], 'message' => 'failed, failed to delete data gifts', 'status' => 'error']);
            }
            return response()->json(['data' => [], 'message' => 'succes, success delete data gift', 'status' => 'success']);
        } catch (\Throwable $th) {
            Log::debug($th);
            return response()->json(['data' => [], 'message' => 'error, internal server error', 'status' => 'error']);
        }
    }
}
