<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\MyCourse;
use App\Help;
use App\Help\help as HelpHelp;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Validation\Rules\Exists;

class MyCourseController extends Controller
{

    public function index(Request $request)
    {
        # code...
        $myCourses = MyCourse::query()->with('course');

        $userID = $request->query('user_id');

        $myCourses->when($userID, function($query) use ($userID)
        {
            return $query->where('user_id', '=', $userID);
        });

         return response()->json([
                'status' => 'success',
                'data' => $myCourses->get()
            ]);
    }

    public function create(Request $request)
    {
        # code...
         $rules = [
            'user_id' =>'required|integer',
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
        $userID = $request->input('user_id');
        $user = getUser($userID);

        if ($user['status'] === 'error') {
            return response()->json([
                'status' => $user['status'],
                'message' => $user['message']],
                $user['http_code']);
        }
        //duplicate data minimalisir
        $isExitCourse = MyCourse::where('course_id', '=', $courseID)
                                ->where('user_id', '=', $userID)
                                ->exists();

        if ($isExitCourse) {
            # code...
            return response()->json([
                'status'=> 'error',
                'message'=> 'Already this Course'
            ], 409);
        }

        //cek kelas premium atau tidak
        if ($course->type === 'premium') {
            if ($course->price === 0) {
                # code...
                return response()->json([
                    'status' => 'error',
                    'message' => 'Price by can\'t is Zero'
                ],405);
            }
            // echo "<prev>".print_r($course->toArray(), 1)."</prev>";
            # code...panggil hepler yang dibuat

            $orders = HelpHelp::postbyOrder([
                'user' => $user['data'],
                'course' => $course->toArray()
            ]);
            //  dd($orders);
            echo "<prev>".print_r($orders, 1)."</prev>";
            if ($orders['status'] === 'error') {
                # code...
                return response()->json([
                    'status' =>$orders['status'],
                    'message' =>$orders['message']
                ], $orders['http_code']);
            }

            return response()->json([
                'status' => $orders['status'],
                'data' => $orders['data']
            ]);

        }else {
            # code...
            $myCourse = MyCourse::create($data);
            return response()->json([
            'status' => 'success',
            'data' => $myCourse
            ]);
        }
    }

    public function createPremiumAccess(Request $request)
    {
        # code...sudah ada id dan validasi
        $data = $request->all();
        $myCourse = MyCourse::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $myCourse
        ]);
    }
}
