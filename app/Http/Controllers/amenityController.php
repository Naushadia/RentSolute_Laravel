<?php

namespace App\Http\Controllers;

use App\Models\amenity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class amenityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('requireUser:api', ['except' => ['allGetAmenity',]]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function postAmenity(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'name' => 'min:5|string',
                'icon.*' => 'required|image|mimes:png,jpg,jpeg,gif,avif|max:10280'
            ]);
            $iconname=[];
            foreach ($request->file('icon') as $icon){
            $imgName = 'rs_' . rand() . '.' . $icon->extension();
            // $path = $request->icon->move(storage_path('profiles/'),$imgName);
            $icon->move(public_path('profiles/'),$imgName);
            $pth = (public_path('profiles/').$imgName);
            $iconname[]=$pth;
            }
            $amenity = amenity::create(['name' => $request['name'], 'icon' => $iconname, 'userId' => $request->user]);
            return response()->json(['status' => 200, 'amenity' => $amenity->icon]);
        } catch (\Exception $ex) {
            return response()->json(['status' => 500, 'message' => $ex->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function getAmenity(Request $request): JsonResponse
    {
        try {
            $user = $request->user;
            $amenities = amenity::where('UserId',$user)->get();
            return response()->json(['status' => 200, 'amenities' => $amenities->pluck('icon')]);
        } catch (\Exception $ex) {
            return response()->json(['status' => 500, 'message' => $ex->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function allGetAmenity(Request $request): JsonResponse
    {
        try {
            $amenities = amenity::select('icon')->get();
            return response()->json(['status' => 200, 'amenities' => $amenities]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyAmenity(Request $request): JsonResponse
    {
        try {
            $id = $request->input('id');
            $user = $request->user;
            if (amenity::where('UserId',$user)->where('id',$id)->exists()) {
                amenity::destroy($id);
                return response()->json(['status' => 200, 'message' => 'amenity deleted successfully']);
            } else {
                return response()->json(['status' => 300, 'message' => 'unauthorized access']);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()]);
        }
    }
}
