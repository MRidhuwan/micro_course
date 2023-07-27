<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChapterController extends Controller
{
    //
    public function index(Request $request)
    {
        # code...
        $chapters = Chapter::query();

        $courseID = $request->query('course_id');

        $chapters->when($courseID, function($query) use ($courseID)
        {
            return $query->where('course_id', '=', $courseID);
        });

         return response()->json([
                'status' => 'success',
                'data' => $chapters->get()
            ]);
    }

    public function show($id)
    {
        # code...
        $chapters = Chapter::find($id);
        if (!$chapters) {
            # code...
            return response()->json([
                'status' => 'error',
                'message' => 'Chapter Not Found'
            ], 404);
        }
        return response()->json([
                'status' => 'success',
                'data' => $chapters
                ]);

    }

    public function create(Request $request)
    {
        # code...
        $rules = [
            'name' =>'required|string',
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
                'message' => 'Courses Not Found'
            ], 404);
        }

        $chapters = Chapter::create($data);

        if ($chapters) {
            # code...
            return response()->json([
                'status' => 'success',
                'message' => $chapters
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        # code...
        $rules = [
            'name' =>'required',
            'course_id' =>'required',
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

        $chapters = Chapter::find($id);
        if (!$chapters) {
            # code...
            return response()->json([
                'status' => 'error',
                'message' => 'Chapters Not Found'
            ], 404);
        }

        $courseID = $request->input('course_id');
        if ($courseID) {
            # code...
            $course = Course::find($courseID);
            if (!$course) {
                # code...
                return response()->json([
                'status' => 'error',
                'message' => 'Course Not Found'
                ], 404);
            }
        }
        $chapters->fill($data);
        $chapters->save();

        return response()->json([
                'status' => 'success',
                'data' => $chapters
            ]);
    }

    public function destroy($id)
    {
        # code...
        $chapters = Chapter::find($id);
        if (!$chapters) {
            # code...
             return response()->json([
                'status' => 'error',
                'message' => 'Chapters Not found'
            ], 404);
        }
        $chapters->delete();
        return response()->json([
                'status' => 'success',
                'message' => 'Deleted course success'
            ]);
    }
}
