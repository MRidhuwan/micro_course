<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\MyCourse;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    //
    public function index()
    {
        # code...
    }

    public function create(Request $request)
    {
        # code...
         $rules = [
            'user_id' =>'required|integer',
            'course_id' =>'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'note' => 'string'
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            # code...
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $courseID = $request->input('course_id');
        $course = Course::find($courseID);

        if (!$course) {
            # code...
             return response()->json([
                'status' => 'error',
                'message' => 'Course Not Found'
            ], 404);
        }

        $userID = $request->input('user_id');
        $user = getUser($userID);

        if ($user['status'] === 'error') {
            return response()->json([
                'status' => $user['status'],
                'message' => $user['message']],
                $user['http_code']);
        }
        //duplicate data minimalisir
        $isExitReview = Review::where('course_id', '=', $courseID)
                                ->where('user_id', '=', $userID)
                                ->exists();

        if ($isExitReview) {
            # code...
            return response()->json([
                'status'=> 'error',
                'message'=> 'ALREADY this Reviews exist'
            ], 409);
        }

        $reviews = Review::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $reviews
        ]);
    }

    public function update(Request $request, $id)
    {
        #
         $rules = [
            'rating' => 'integer|min:1|max:5',
            'note' => 'string'
        ];

        $data = $request->except('course_id', 'user_id');

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            # code...
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $reviews = Review::find($id);

        if (!$reviews) {
            # code...
             return response()->json([
                'status' => 'error',
                'message' => 'Reviews Not Found'
            ], 404);
        }

        $reviews->fill($data);
        $reviews->save();

        return response()->json([
            'status' => 'success',
            'data' => $reviews
        ]);
    }

    public function destroy($id)
    {
        # code...
        $reviews = Review::find($id);

         if (!$reviews) {
            # code...
            return response()->json([
                'status' => 'error',
                'message' => 'Reviews Not Found'
            ], 404);
        }

        $reviews->delete();
        return response()->json([
                'status' => 'success',
                'message' => 'Reviews Delete Success'
            ]);

    }
}
