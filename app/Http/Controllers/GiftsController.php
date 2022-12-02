<?php

namespace App\Http\Controllers;

use App\Gifts;
use App\Http\Resources\Gifts as ResourcesGifts;
use App\Http\Resources\GiftsCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GiftsController extends Controller
{
    public function index(Request $request)
    {
        $limit = NULL;
        $orderBy = 'rating';
        $sortBy = 'asc';

        if ($request->has('limit')) {
            $limit = $request->input('limit');
        }
        if ($request->has('order')) {
            if ($request->input('order') == 'new') {
                $orderBy = 'new_gift';
            } else {
                $orderBy = $request->input('order');
            }
        }
        if ($request->has('sort')) {
            $sortBy = $request->input('sort');
        }

        $data = Gifts::orderBy($orderBy, $sortBy)->paginate($limit ?? config('amount_data_page'));
        try {
            return new GiftsCollection($data);
        } catch (\Throwable $th) {
            Log::debug($th);
            return response()->json(['data' => [], 'message' => 'error, internal server error', 'status' => 'error']);
        }
    }

    public function show($id)
    {
        try {
            if (!Gifts::find($id)) {
                return response()->json(['data' => [], 'message' => 'failed, data gifts not found', 'status' => 'error']);
            }
            return new ResourcesGifts(Gifts::find($id));
        } catch (\Throwable $th) {
            Log::debug($th);
            return response()->json(['data' => [], 'message' => 'error, internal server error', 'status' => 'error']);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'description' => 'required',
                'price' => 'required',
                'new_gift' => 'required',
                'image_1' => 'required|image|max:1024|mimes:jpeg,bmp,png',
            ]);

            if ($validator->fails()) {
                return response()->json(['data' => [], 'message' => $validator->errors(), 'status' => 'error',]);
            }
            $name = $request->input('name');
            $description = $request->input('description');
            $price = $request->input('price');
            $is_new = $request->input('new_gift');

            $images = [];
            for ($i = 1; $i <= config('images_number'); $i++) {

                if ($request->hasFile("image_$i")) {
                    if ($request->file("image_$i")->isValid()) {

                        $image = $request->file("image_$i");
                        $image_store = Storage::put(
                            'public',
                            $image
                        );

                        $url = Storage::url($image_store);
                        array_push($images, $url);
                    }
                }
            }



            $gifts = new Gifts;
            $gifts->name = $name;
            $gifts->description = $description;
            $gifts->price = $price;
            $gifts->new_gift = $is_new;
            $gifts->images = json_encode($images);
            $gifts->save();

            return new ResourcesGifts($gifts);
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
