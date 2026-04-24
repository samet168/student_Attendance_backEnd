<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;

class SubjectController extends Controller
{
    // 📌 1. index (get all)
    public function index()
    {
        $subjects = Subject::all();

        return response()->json([
            'status' => true,
            'message' => 'All subjects',
            'data' => $subjects
        ]);
    }

    // 📌 2. list (pagination + search)
    public function list(Request $request)
    {
        $query = Subject::query();

        // 🔎 search by name or code
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('subject_name', 'like', '%' . $request->search . '%')
                  ->orWhere('subject_code', 'like', '%' . $request->search . '%');
            });
        }

        $subjects = $query->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Subject list',
            'data' => $subjects
        ]);
    }

    // 📌 3. store (create)
    public function store(Request $request)
    {
        $request->validate([
            'subject_name' => 'required|string|max:255',
            'subject_code' => 'required|string|max:255|unique:subjects'
        ]);

        $subject = Subject::create([
            'subject_name' => $request->subject_name,
            'subject_code' => $request->subject_code,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Subject created successfully',
            'data' => $subject
        ], 201);
    }

    // 📌 4. show (get by id)
    public function show($id)
    {
        $subject = Subject::find($id);

        if (!$subject) {
            return response()->json([
                'status' => false,
                'message' => 'Subject not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $subject
        ]);
    }

    // 📌 5. update
    public function update(Request $request, $id)
    {
        $subject = Subject::find($id);

        if (!$subject) {
            return response()->json([
                'status' => false,
                'message' => 'Subject not found'
            ], 404);
        }

        $request->validate([
            'subject_name' => 'required|string|max:255',
            'subject_code' => 'required|string|max:255|unique:subjects,subject_code,' . $id
        ]);

        $subject->subject_name = $request->subject_name;
        $subject->subject_code = $request->subject_code;
        $subject->save();

        return response()->json([
            'status' => true,
            'message' => 'Subject updated successfully',
            'data' => $subject
        ]);
    }

    // 📌 6. destroy (delete)
    public function destroy($id)
    {
        $subject = Subject::find($id);

        if (!$subject) {
            return response()->json([
                'status' => false,
                'message' => 'Subject not found'
            ], 404);
        }

        $subject->delete();

        return response()->json([
            'status' => true,
            'message' => 'Subject deleted successfully'
        ]);
    }
}