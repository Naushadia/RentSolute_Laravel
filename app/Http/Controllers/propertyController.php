<?php

namespace App\Http\Controllers;

use App\Models\image;
use App\Models\property;
use App\Models\propertyAmenity;
use App\Models\propertyImages;
use App\Models\propertyQuestion;
use App\Models\ROOM;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Ramsey\Uuid\v1;

class propertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('requireUser:api', ['except' => ['getProperty','allGetProperty','allGetImage']]);
    }


    public function image(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'caption' => 'required|min:3|string',
                'image.*' => 'required|image|mimes:png,jpg,jpeg,gif,avif|max:10280',
            ]);

            foreach($request->file('image') as $image) {
                $obj = array(
                    $imgName = 'rs_' . rand() . '.' . $image->extension(),
                    $image->move(public_path('profiles/'),$imgName),
                    $pth = (public_path('profiles/').$imgName),
                    'UserId' => $request->user,
                    'caption' => $request->caption,
                    'image' => $pth,
                    'filename' => $imgName
                );
                image::create($obj);
            };
            return response()->json(['status' => 200, 'message' =>'image created successfully']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function postProperty(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'postal_code' => 'required|min:6|integer',
                'name' => 'min:3|string',
                'city' => 'required|string',
                "state" => 'required|string',
                'country' => 'required|string',
                "property_type" => 'required|integer',
                "description" => 'required|string',
                "tenancy_status" => 'required|integer',
                'street' => 'required|string',
                'latitude' => 'required|integer',
                'longitude' => 'required|integer',
                'furnishing_status' => 'required|integer',
                'furnishing_details' => 'required|string',
                'area' => 'required|string',
                'share_property_url' => 'required|url',
                'images.*' => 'required|array',
                'amenities.*' => 'required|array',
                'questions.*' => 'required|array',
                'rooms.*' => 'required|array',
            ]);
            $property = new property;

            $property->UserId = $request->user;
            $property->name = $request->name;
            $property->property_type = $request->property_type;
            $property->description = $request->description;
            $property->tenancy_status = $request->tenancy_status;
            $property->street = $request->street;
            $property->city = $request->city;
            $property->state = $request->state;
            $property->postal_code = $request->postal_code;
            $property->country = $request->country;
            $property->latitude = $request->latitude;
            $property->longitude = $request->longitude;
            $property->furnishing_status = $request->furnishing_status;
            $property->furnishing_detailes = $request->furnishing_details;
            $property->share_property_url = $request->share_property_url;
            $property->area = $request->area;

            $property->save();

            foreach($request->images as $image) {
                $obj = array(
                    'propertyId' => $property->id,
                    'imageId' => $image['image'],
                );

                propertyImages::create($obj);
            }

            foreach($request->amenities as $amenity) {
                $obj = array(
                    'propertyId' => $property->id,
                    'amenityId' => $amenity['amenity'],
                );

                propertyAmenity::create($obj);
            }

            foreach($request->questions as $question) {
                $obj = array(
                    'propertyId' => $property->id,
                    'questionId' => $question['questionId'],
                    'optionId' => $question['option_id'],
                    'preferred' => $question['preferred'],
                );

                propertyQuestion::create($obj);
            }

            foreach($request->rooms as $room) {
                $obj = array(
                    'propertyId' => $property->id,
                    'imageId' => $room['image'],
                    'name' => $room['name'],
                    'url' => $room['url'],
                    'room_type' => $room['type'],
                    'caption' => $room['caption'],
                );

                ROOM::create($obj);
            }

            $property_obj = property::with('rooms', 'property_amenities','property_images','property_questions','property_question_options')->find($property->id);

            return response()->json(['msg' => 'Property created successfully', 'data' => $property_obj]);
        } catch (\Exception $ex) {
            return response()->json(['status' => 500, 'message' => $ex->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function getProperty(Request $request): JsonResponse
    {
        try {
            $user =  Auth::user();
            $property = $user->properties()->with('rooms', 'property_amenities','property_images','property_questions','property_question_options')->get();
            $count = $property->count();
            return response()->json(['status' => 200, 'properties' => $property, 'count' => $count]);
        } catch (\Exception $ex) {
            return response()->json(['status' => 500, 'message' => $ex->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function destroyImage(Request $request): JsonResponse
    {
        try {
            $id = $request->route('id');
            $user = $request->user;
            if (image::where('UserId',$user)->where('id',$id)->exists()) {
                image::destroy($id);
                return response()->json(['status' => 200, 'message' => 'image deleted successfully']);
            } else {
                return response()->json(['status' => 300, 'message' => 'unauthorized access']);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()]);
        }
    }

    public function allGetProperty(Request $request): JsonResponse
    {
        try {
            $properties = property::with('rooms', 'property_amenities','property_images','property_questions','property_question_options')->paginate(1);
            return response()->json(['status' => 200, 'amenities' => $properties]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            $id = $request->route('id');
            $user = $request->user;
            if (property::where('UserId',$user)->where('id',$id)->exists()) {
                property::destroy($id);
                return response()->json(['status' => 200, 'message' => 'property deleted successfully']);
            } else {
                return response()->json(['status' => 300, 'message' => 'unauthorized access']);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()]);
        }
    }

    public function allGetImage(Request $request): JsonResponse
    {
        try {
            $images = image::paginate(3);
            return response()->json(['status' => 200, 'amenities' => $images]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()]);
        }
    }

    public function getImage(Request $request): JsonResponse
    {
        try {
            $user = $request->user;
            $images = image::where('UserId',$user)->get();
            return response()->json(['status' => 200, 'images' => $images]);
        } catch (\Exception $ex) {
            return response()->json(['status' => 500, 'message' => $ex->getMessage()]);
        }
    }

    public function putProperty(Request $request): JsonResponse
    {

        try {
            $property = property::where('id', $request->id)->where('UserId', $request->user)->with('rooms', 'property_amenities','property_images','property_questions','property_question_options')->first();

            $property->update($request->except(['rooms','questions','amenities','images']));
            // $property->update($request->only());

            foreach($request->images as $image) {
                $obj = array(
                    'propertyId' => $property->id,
                    'imageId' => $image['image'],
                );

                $property_images = propertyImages::where('propertyId', $request->id)->first();

                $property_images->update($obj);
            }

            foreach($request->amenities as $amenity) {
                $obj = array(
                    'propertyId' => $property->id,
                    'amenityId' => $amenity['amenity'],
                );

               $property_amenities = propertyAmenity::where('propertyId', $request->id)->first();

               $property_amenities->update($obj);
            }

            foreach($request->questions as $question) {
                $obj = array(
                    'propertyId' => $property->id,
                    'questionId' => $question['questionId'],
                );

                $property_questions = propertyQuestion::where('propertyId', $request->id)->first();

                $property_questions->update($obj);
            }

            foreach($request->rooms as $room) {
                $obj = array(
                    'propertyId' => $property->id,
                    'imageId' => $room['image'],
                    'name' => $room['name'],
                    'url' => $room['url'],
                    'room_type' => $room['type'],
                    'caption' => $room['caption'],
                );

                $rooms = ROOM::where('propertyId', $request->id)->first();

                $rooms->update($obj);
            }
            $property->load('rooms', 'property_amenities','property_images','property_questions','property_question_options');

            return response()->json(['msg' => $property]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()]);
        }
    }
}
