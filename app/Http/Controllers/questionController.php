<?php

namespace App\Http\Controllers;

use App\Models\option;
use App\Models\question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class questionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('requireUser:api', ['except' => ['allGetQuestion']]);
    }


    public function addQuestion(Request $request): JsonResponse
    {
        try {
            $question = new question;

            $question->UserId = $request->user;
            $question->title = $request->title;
            $question->type = $request->type;
            $question->has_other = $request->has_other;

            $question->save();

            foreach($request->options as $option) {
                $obj = array(
                    'questionId' => $question->id,
                    'text' => $option['text'],
                    'preferred' => $option['preferred']
                );

                option::create($obj);
            }

            $question_obj = question::with('options')->find($question->id);

            return response()->json(['msg' => 'Question created successfully', 'data' => $question_obj]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function getQuestions(Request $request): JsonResponse
    {
        try {
            $questions = question::where('UserId', $request->user)->with('options')->get();

            return response()->json(['msg' => 'Question found successfully','questions' => $questions]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()]);
        }
    }

    public function destroyQuestion(Request $request): JsonResponse
    {
        try {
            $id = $request->input('id');
            $user = $request->user;
            if (question::where('UserId',$user)->where('id',$id)->exists()) {
                question::destroy($id);
                return response()->json(['status' => 200, 'message' => 'question deleted successfully']);
            } else {
                return response()->json(['status' => 300, 'message' => 'unauthorized access']);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()]);
        }
    }

    public function allGetQuestion(Request $request): JsonResponse
    {
        try {
            $question = question::all();
            return response()->json(['status' => 200, 'question' => $question]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()]);
        }
    }
}
