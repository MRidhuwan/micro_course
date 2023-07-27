<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ImageCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
    //

    public function create(Request $request)
    {
        # code...
         $rules = [
            'image' =>'required|url',
            'course_id' =>'required|integer',
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

        $imageCourse = ImageCourse::create($data);
        return response()->json([
                'status' => 'success',
                'data' => $imageCourse
            ]);
    }

     public function destroy($id)
    {
        # code...
        $imageCourse = ImageCourse::find($id);
        if (!$imageCourse) {
            # code...
             return response()->json([
                'status' => 'error',
                'message' => 'ImageCourse Not found'
            ], 404);
        }
        $imageCourse->delete();
        return response()->json([
                'status' => 'success',
                'message' => 'Deleted imageCourse success'
            ]);
    }
}
