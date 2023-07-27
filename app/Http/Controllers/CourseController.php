<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Mentor;
use App\Models\MyCourse;
use App\Models\Review;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    //
    public function index(Request $request)
    {
        # code...
        $courses = Course::query();

        $search = $request->query('q');
        $status = $request->query('status');

        $courses->when($search, function($query) use ($search) {
            return $query->whereRaw("name LIKE '%".strtolower($query)."%");
        });

        $courses->when($status, function($query) use ($status) {
            return $query->where("status", "=", $status);
        });
         return response()->json([
                'status' => 'success',
                'data' => $courses->paginate(10)
            ]);
    }
    public function show($id)
    {
        # code...
        $courses = Course::with([ 'chapters.lessons',
        'mentor', 'images'])
        ->find($id);

        if (!$courses) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course Not Found'
            ]);
        }

        $reviews = Review::where('course_id', '=', $id)->get()->toArray();
        $totalStudent = MyCourse::where('course_id', '=', $id)->count();

        $courses['review'] = $reviews;
        $courses['total_student'] = $totalStudent;

        return response()->json([
                'status' => 'success',
                'data' => $courses
            ]);
    }

    public function create(Request $request)
    {
        # code...
        $rules = [
            'name' =>'required|string',
            'thumbnail' =>'url',
            'certificate' =>'required|boolean',
            'type' =>'required|in:free,premium',
            'status' =>'required|in:draft,published',
            'level' =>'required|in:all-level,pemula,sedang,profesional',
            'price' =>'integer',
            'descriptions' =>'string',
            'mentor_id' =>'required|integer',
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
        $mentorID = $request->input('mentor_id');

        $mentor = Mentor::find($mentorID);

        if (!$mentor) {
            # code...
            return response()->json([
                'status' => 'error',
                'message' => 'Mentor Not Found'
            ], 404);
        }

        $courses = Course::create($data);

        if ($courses) {
            # code...
            return response()->json([
                'status' => 'success',
                'data' => $courses
            ]);
        }

    }

    public function update(Request $request, $id)
    {
        # code...
         $rules = [
            'name' =>'string',
            'thumbnail' =>'url',
            'certificate' =>'boolean',
            'type' =>'in:free,premium',
            'status' =>'in:draft,published',
            'level' =>'in:all-level,pemula,sedang,profesional',
            'price' =>'integer',
            'descriptions' =>'string',
            'mentor_id' =>'integer',
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

        $course = Course::find($id);
        if (!$course) {
            # code...
            return response()->json([
                'status' => 'error',
                'message' => 'Courses Not Found'
            ], 404);
        }

        $mentorID = $request->has('mentor_id');

        if ($mentorID) {
            # code...
            $mentor = Mentor::find($mentorID);
            if (!$mentor) {
                # code...
                return response()->json([
                'status' => 'error',
                'message' => 'Mentor Not Found'
                ], 404);
            }
        }

        $course->fill($data);

         $course->save();
        return response()->json([
                'status' => 'success',
                'data' => $course
            ]);
    }

     public function destroy($id)
    {
        # code...
        $courses = Course::find($id);

         if (!$courses) {
            # code...
            return response()->json([
                'status' => 'error',
                'message' => 'Courses Not Found'
            ], 404);
        }

        $courses->delete();
        return response()->json([
                'status' => 'success',
                'message' => 'Deleted course success'
            ]);

    }
}
