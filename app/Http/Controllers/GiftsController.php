<?php

namespace App\Http\Controllers;

use App\Gifts;
use App\Http\Resources\Gifts as ResourcesGifts;
use App\Http\Resources\GiftsCollection;
use App\RedemTransactions;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class GiftsController extends Controller
{
    const EMPTY_QUANTITY = 0;
    const REDEM_AMOUNT = 1;
    public function __construct()
    {
    }
    public function index(Request $request)
    {
        try {
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
                'quantity' => 'required|numeric',
                'image_1' => 'required|image|max:1024|mimes:jpeg,bmp,png',
            ]);

            if ($validator->fails()) {
                return response()->json(['data' => [], 'message' => $validator->errors(), 'status' => 'error',]);
            }
            $name = $request->input('name');
            $description = $request->input('description');
            $price = $request->input('price');
            $is_new = $request->input('new_gift');
            $quantity = $request->input('quantity');

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
            $gifts->quantity = $quantity;
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

    public function update_patch(Request $request, $id)
    {
        try {
            $gifts = Gifts::findOrFail($id);

            $gifts->fill($request->all());
            if ($gifts->isClean()) {
                return response()->json(['data' => [], 'message' => 'at least one value must change', 'status' => 'error']);
            }
            $gifts->save();
            return new ResourcesGifts($gifts);
        } catch (\Throwable $th) {
            Log::debug($th);
            return response()->json(['data' => [], 'message' => 'error, internal server error', 'status' => 'error']);
        }
    }

    public function update_put(Request $request, $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'description' => 'required',
                'price' => 'required',
                'new_gift' => 'required',
                'quantity' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                return response()->json(['data' => [], 'message' => $validator->errors(), 'status' => 'error',]);
            }

            $gifts = Gifts::findOrFail($id);
            $gifts->fill($request->all());
            $gifts->save();
            return new ResourcesGifts($gifts);
        } catch (\Throwable $th) {
            Log::debug($th);
            return response()->json(['data' => [], 'message' => 'error, internal server error', 'status' => 'error']);
        }
    }

    public function redem($id)
    {
        try {
            $gift = Gifts::find($id);
            if ($gift) {
                if ($gift->quantity == self::EMPTY_QUANTITY) {
                    return response()->json(['data' => [], 'message' => 'Stock gifts' . $gift->name . ' are no longer available', 'status' => 'failed',]);
                }

                // check if user already redem this gifts 
                $user_already_redem = RedemTransactions::where('users_id', auth()->id())->where('gifts_id', $gift->id)->first();
                if ($user_already_redem) {
                    return response()->json(['data' => [], 'message' => 'You already redem this gifts', 'status' => 'failed']);
                }

                // insert data into redem transaction
                $redem_transaction = new RedemTransactions();
                $redem_transaction->gifts_id = $gift->id;
                $redem_transaction->users_id = auth()->id();
                $redem_transaction->save();

                // update quantity gifts
                $gift->quantity = ($gift->quantity - self::REDEM_AMOUNT);
                $gift->save();
            } else {
                return response()->json(['data' => [], 'message' => 'gifts are no longer available', 'status' => 'failed',]);
            }
        } catch (\Throwable $th) {
            Log::debug($th);
            return response()->json(['data' => [], 'message' => 'error, internal server error', 'status' => 'error']);
        }
    }

    public function rating(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'rating' => 'required|numeric|max:5'
            ]);

            if ($validator->fails()) {
                return response()->json(['data' => [], 'message' => $validator->errors(), 'status' => 'error',]);
            }
            $rating = $request->input('rating');

            $gifts = Gifts::find($id);
            if ($gifts) {
                $gifts->rating = (($gifts->rating + $rating) / 2);
                $gifts->save();
            } else {
                return response()->json(['data' => [], 'message' => 'gifts are no longer available', 'status' => 'error',]);
            }
        } catch (\Throwable $th) {
            Log::debug($th);
            return response()->json(['data' => [], 'message' => 'error, internal server error', 'status' => 'error']);
        }
    }

    public function redems(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'gifts' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['data' => [], 'message' => $validator->errors(), 'status' => 'error',]);
            }
            $gifts = $request->input('gifts');
            foreach ($gifts as $id_gift) {
                $gift = Gifts::find($id_gift);

                if ($gift) {
                    if ($gift->quantity == self::EMPTY_QUANTITY) {
                        return response()->json(['data' => [], 'message' => 'Stock gifts ' . $gift->name . ' are no longer available', 'status' => 'failed',]);
                    }

                    // check if user already redem this gifts 
                    $user_already_redem = RedemTransactions::where('users_id', auth()->id())->where('gifts_id', $gift->id)->first();
                    if ($user_already_redem) {
                        return response()->json(['data' => [], 'message' => 'You already redem this gift ' . $gift->name, 'status' => 'failed']);
                    }

                    // insert data into redem transaction
                    $redem_transaction = new RedemTransactions();
                    $redem_transaction->gifts_id = $gift->id;
                    $redem_transaction->users_id = auth()->id();
                    $redem_transaction->save();

                    // update quantity gift
                    $gift->quantity = ($gift->quantity - self::REDEM_AMOUNT);
                    $gift->save();
                } else {
                    return response()->json(['data' => [], 'message' => 'gifts are no longer available', 'status' => 'failed',]);
                }
            }
        } catch (\Throwable $th) {
            Log::debug($th);
            return response()->json(['data' => [], 'message' => 'error, internal server error', 'status' => 'error']);
        }
    }
}
