<?php
namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ClassroomController extends Controller
{


public function index(Request $request)
{
    $user = Auth::user();

    $query = Classroom::query();

    //  TEACHER
    if ($user->role === 'teacher') {

        $query->where('teacher_id', 'all', [$user->id]);
    }

    //  ADMIN
    if ($user->role === 'admin' && $request->teacher_id) {

        $query->where('teacher_id', 'all', [$request->teacher_id]);
    }

    return response()->json([
        'message' => 'Classrooms fetched successfully',
        'data' => $query->latest()->get()
    ]);
}




    public function getTeachers()
        {
            $teachers = User::where('role', 'teacher')->get();

            return response()->json([
                'data' => $teachers
            ]);
        }





    // 📌 CREATE CLASS
// public function store(Request $request)
// {
//     // 1. Validate input
//     $request->validate([
//         'name' => 'required|string|max:255',
//         'room_number' => 'nullable|string|max:255',
//         'teacher_id' => 'nullable|exists:users,id',
//     ]);

//     // 2. Create new classroom
//     $classroom = Classroom::create([
//         'name' => $request->name,
//         'room_number' => $request->room_number,
//         'teacher_id' => $request->teacher_id,
//     ]);

//     // 3. Response
//     return response()->json([
//         'message' => 'Classroom created successfully',
//         'data' => $classroom
//     ], 201);
// }


public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'room_number' => 'nullable|string|max:255',
        'teacher_ids' => 'required|array',
        'teacher_ids.*' => 'exists:users,id',
    ]);

    // 1. create classroom
    $classroom = Classroom::create([
        'name' => $request->name,
        'room_number' => $request->room_number,
    ]);

    // 2. attach teachers (array → pivot)
    $classroom->teachers()->attach($request->teacher_ids);

    return response()->json([
        'message' => 'Class created successfully',
        'data' => $classroom->load('teachers')
    ], 201);
}

    // 📌 SHOW
public function show($id)
{
    $class = Classroom::with('teachers')->find($id);

    if (!$class) {
        return response()->json(['message' => 'Not found'], 404);
    }

    return response()->json([
        'status' => true,
        'data' => $class
    ]);
}

    // 📌 UPDATE
public function update(Request $request, $id)
{
    $class = Classroom::find($id);

    if (!$class) {
        return response()->json(['message' => 'Not found'], 404);
    }

    // update normal fields
    $class->update([
        'name' => $request->name,
        'room_number' => $request->room_number,
    ]);

    // ✅ sync teachers
    if ($request->has('teacher_ids')) {
        $class->teachers()->sync($request->teacher_ids);
    }

    return response()->json([
        'status' => true,
        'message' => 'Updated successfully',
        'data' => $class
    ]);
}

    // 📌 DELETE
    public function destroy($id)
    {
        $class = Classroom::find($id);

        if (!$class) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $class->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }
}