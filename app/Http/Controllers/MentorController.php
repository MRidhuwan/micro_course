<?php

namespace App\Http\Controllers;

use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MentorController extends Controller
{
    //
    public function index()
    {
        # code...
        $mentors = Mentor::all();
         return response()->json([
                'status' => 'success',
                'data' => $mentors
            ]);
    }
    public function show($id)
    {
        # code...
        $mentor = Mentor::find($id);
         if (!$mentor) {
            # code...
            return response()->json([
                'status' => 'error',
                'message' => 'Mentor Not Found'
            ]);
        }

        return response()->json([
                'status' => 'success',
                'data' => $mentor
            ]);

    }

    public function create(Request $request)
    {
        # code...
        $rules = [
            'name' => 'required|string',
            'profile' => 'required|url',
            'profession' => 'required|string',
            'email' => 'required|email',
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

        $mentor = Mentor::create($data);

        return response()->json([
                'status' => 'success',
                'data' => $mentor,
            ]);
    }

    public function update(Request $request, $id)
    {
        # code...
         $rules = [
            'name' => 'string',
            'profile' => 'url',
            'profession' => 'string',
            'email' => 'email',
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

        $mentor = Mentor::find($id);
        if (!$mentor) {
            # code...
            return response()->json([
                'status' => 'error',
                'message' => 'Mentor Not Found'
            ], 404);
        }

        $mentor->fill($data);
        $mentor->save();
        return response()->json([
                'status' => 'success',
                'data' => $mentor
            ]);
    }

    public function destroy($id)
    {
        # code...
        $mentors = Mentor::find($id);

         if (!$mentors) {
            # code...
            return response()->json([
                'status' => 'error',
                'message' => 'Mentor Not Found'
            ], 404);
        }

        $mentors->delete();
        return response()->json([
                'status' => 'success',
                'message' => 'Mentor Delete Success'
            ]);

    }
}
