<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    //

    public function index(Request $request)
    {
        # code...
        $lessons = Lesson::query();

        $chapterID = $request->query('chapter_id');

        $lessons->when($chapterID, function($query) use ($chapterID)
        {
            return $query->where('chapter_id', '=', $chapterID);
        });

         return response()->json([
                'status' => 'success',
                'data' => $lessons->get()
            ]);
    }

    public function show($id)
    {
        # code...
        $lesson = Lesson::find($id);
        if (!$lesson) {
            # code...
             return response()->json([
                'status' => 'error',
                'message' => 'Lesson Not found'
            ], 404);
        }

        return response()->json([
                'status' => 'success',
                'data' => $lesson
            ]);


    }


    public function create(Request $request)
    {
        # code...
         $rules = [
            'name' =>'required|string',
            'video' =>'required|string',
            'chapter_id' =>'required|integer',
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

        $chapterID = $request->input('chapter_id');
        $chapter = Chapter::find($chapterID);

        if (!$chapter) {
            # code...
             return response()->json([
                'status' => 'error',
                'message' => 'chapter Not Found'
            ], 404);
        }

        $lesson = Lesson::create($data);
        return response()->json([
                'status' => 'success',
                'data' => $lesson
            ]);
    }

    public function update(Request $request, $id)
    {
        # code...
        $rules = [
            'name' =>'string',
            'video' => 'string',
            'chapter_id' => 'integer'
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

        $lesson = Lesson::find($id);
        if (!$lesson) {
            # code...
            return response()->json([
                'status' => 'error',
                'message' => 'lesson Not Found'
            ], 404);
        }

        $chapterID = $request->input('chapter_id');
        if ($chapterID) {
            # code...
            $chapter = Chapter::find($chapterID);
            if (!$chapter) {
                # code...
                return response()->json([
                'status' => 'error',
                'message' => 'Chapter Not Found'
                ], 404);
            }
        }
        $lesson->fill($data);
        $lesson->save();

        return response()->json([
                'status' => 'success',
                'data' => $lesson
            ]);
    }

    public function destroy($id)
    {
        # code...
        $lesson = Lesson::find($id);
        if (!$lesson) {
            # code...
             return response()->json([
                'status' => 'error',
                'message' => 'Lesson Not found'
            ], 404);
        }
        $lesson->delete();
        return response()->json([
                'status' => 'success',
                'message' => 'Deleted Lesson success'
            ]);
    }
}
