<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    //
    public function index()
    {
       $classrooms = Classroom::all();
        if($classrooms == null){
            return response()->json([
                'status' => false,
                'message' => 'No classrooms found'
            ], 404);
        }
        else{
            return response()->json([
                'status' => true,
                'data' => $classrooms
            ], 200);
        }
    }

    public function list(Request $request)
    {
        $query = Classroom::query();

        // 🔎 SEARCH by name
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 📄 PAGINATION
        $classrooms = $query->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Classroom list',
            'data' => $classrooms
        ], 200);
    }
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'room_number' => 'nullable|string|max:255',
    ]);

    $classroom = Classroom::create([
        'name' => $request->name,
        'room_number' => $request->room_number,
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Classroom created successfully',
        'data' => $classroom
    ], 201);
}

    public function show($id)
    {
        $classroom = Classroom::find($id);
        if($classroom == null){
            return response()->json([
                'status' => false,
                'message' => 'Classroom not found'
            ], 404);
        }
        else{
            return response()->json([
                'status' => true,
                'data' => $classroom
            ], 200);
        }
    }
    public function update(Request $request, $id)
    {
        $classroom = Classroom::find($id);

        if (!$classroom) {
            return response()->json([
                'status' => false,
                'message' => 'Classroom not found'
            ], 404);
        }

        $classroom->name = $request->name;
        $classroom->room_number = $request->room_number; // ✔️ only this

        $classroom->save();

        return response()->json([
            'status' => true,
            'message' => 'Classroom updated successfully',
            'data' => $classroom
        ]);
    }

    public function destroy($id)
    {
        $classroom = Classroom::find($id);

        if (!$classroom) {
            return response()->json([
                'status' => false,
                'message' => 'Classroom not found'
            ], 404);
        }

        $classroom->delete();

        return response()->json([
            'status' => true,
            'message' => 'Classroom deleted successfully'
        ], 200);
    }

}
