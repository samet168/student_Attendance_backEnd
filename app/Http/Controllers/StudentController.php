<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class StudentController extends Controller
{
    //
public function index(Request $request)
{
    $query = Student::query();

    if ($request->classroom_id) {
        $query->where('classroom_id', $request->classroom_id);
    }

   
    $students = $query->with(['classroom'])->get();

    return response()->json(['data' => $students]);
}

    public function list(Request $request)
    {
        $query = Student::query();

     
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $students = $query->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Student list',
            'data' => $students
        ], 200);
    }
// public function store(Request $request)
// {
//     $request->validate([
//         'classroom_id' => 'required|exists:classrooms,id',
//         'student_id_card' => 'required|unique:students',
//         'name' => 'required|string|max:255',
//         'gender' => 'required|in:Male,Female',
//         'phone' => 'nullable|string|max:20',
//     ]);

//     $student = Student::create([
//         'classroom_id' => $request->classroom_id,
//         'student_id_card' => $request->student_id_card,
//         'name' => $request->name,
//         'gender' => $request->gender,
//         'phone' => $request->phone,
//     ]);

//     return response()->json([
//         'status' => true,
//         'message' => 'Student created successfully',
//         'data' => $student
//     ], 201);
// }

public function store(Request $request)
{
    // 1. Validate data
    $request->validate([
        'classroom_id' => 'required|exists:classrooms,id',
        'student_id_card' => 'required|string|unique:students,student_id_card',
        'name' => 'required|string|max:255',
        'gender' => 'required|in:Male,Female',
        'phone' => 'nullable|string|max:20',
    ]);

    // 2. Create student
    $student = Student::create([
        'classroom_id' => $request->classroom_id,
        'student_id_card' => $request->student_id_card,
        'name' => $request->name,
        'gender' => $request->gender,
        'phone' => $request->phone,
    ]);

    // 3. Response
    return response()->json([
        'message' => 'Student created successfully',
        'data' => $student
    ], 201);
}
    public function show($id)
    {
        $student = Student::find($id,['*']);
        if($student == null){
            return response()->json([
                'status' => false,
                'message' => 'Student not found'
            ], 404);
        }
        else{
            return response()->json([
                'status' => true,
                'data' => $student
            ], 200);
        }
    }
// public function update(Request $request, $id)
// {
//     $student = Student::find($id);

//     if (!$student) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Student not found'
//         ], 404);
//     }

//     $request->validate([
//         'classroom_id' => 'required|exists:classrooms,id',
//         'student_id_card' => 'required|unique:students,student_id_card,' . $id,
//         'name' => 'required|string|max:255',
//         'gender' => 'required|in:Male,Female',
//         'phone' => 'nullable|string|max:20',
//     ]);

//     $student->classroom_id = $request->classroom_id;
//     $student->student_id_card = $request->student_id_card;
//     $student->name = $request->name;
//     $student->gender = $request->gender;
//     $student->phone = $request->phone;

//     $student->save();

//     return response()->json([
//         'status' => true,
//         'message' => 'Student updated successfully',
//         'data' => $student
//     ], 200);
// }


public function update(Request $request, $id)
{
    $student = Student::find($id);

    if (!$student) {
        return response()->json([
            'status' => false,
            'message' => 'រកមិនឃើញទិន្នន័យសិស្សឡើយ'
        ], 404);
    }

    // 💡 កែសម្រួលលក្ខខណ្ឌត្រង់ unique ឱ្យស្គាល់ String ID (MongoDB/UUID)
    $request->validate([
        'classroom_id'    => 'required|exists:classrooms,id',
        'student_id_card' => [
            'required',
            // 💡 បញ្ជាក់ប្រាប់ Laravel ថា ឱ្យ ignore $id ទៅលើ column 'id' (ឬ '_id' បើប្រើ MongoDB)
            Rule::unique('students', 'student_id_card')->ignore($id, 'id'), 
        ],
        'name'            => 'required|string|max:255',
        'gender'          => 'required|in:Male,Female',
        'phone'           => 'nullable|string|max:20',
    ]);

    // រក្សាទុកទិន្នន័យ
    $student->classroom_id = $request->classroom_id;
    $student->student_id_card = $request->student_id_card;
    $student->name = $request->name;
    $student->gender = $request->gender;
    $student->phone = $request->phone;
    $student->save();

    return response()->json([
        'status' => true,
        'message' => 'Student updated successfully',
        'data' => $student
    ], 200);
}
    public function destroy($id)
    {
        $student = Student::find($id,['*']);

        if (!$student) {
            return response()->json([
                'status' => false,
                'message' => 'Student not found'
            ], 404);
        }

        $student->delete();
        return response()->json([
            'status' => true,
            'message' => 'Student deleted successfully'
        ], 200);
    }
}
