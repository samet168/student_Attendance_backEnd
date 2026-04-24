<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    // 📌 1. index (get all)
    public function index()
    {
        $attendances = Attendance::with(['student', 'subject', 'user'])->get();

        return response()->json([
            'status' => true,
            'message' => 'All attendance',
            'data' => $attendances
        ]);
    }

    // 📌 2. list (pagination + search)
    public function list(Request $request)
    {
        $query = Attendance::with(['student', 'subject', 'user']);

        // 🔎 search by student name or subject
        if ($request->search) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhereHas('subject', function ($q) use ($request) {
                $q->where('subject_name', 'like', '%' . $request->search . '%');
            });
        }

        $attendances = $query->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Attendance list',
            'data' => $attendances
        ]);
    }

    // 📌 3. store (create)
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'user_id' => 'required|exists:users,id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:Present,Absent,Late,Permission',
            'remarks' => 'nullable|string'
        ]);

        $attendance = Attendance::create([
            'student_id' => $request->student_id,
            'subject_id' => $request->subject_id,
            'user_id' => $request->user_id,
            'attendance_date' => $request->attendance_date,
            'status' => $request->status,
            'remarks' => $request->remarks,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Attendance created successfully',
            'data' => $attendance
        ], 201);
    }

    // 📌 4. show
    public function show($id)
    {
        $attendance = Attendance::with(['student', 'subject', 'user'])->find($id);

        if (!$attendance) {
            return response()->json([
                'status' => false,
                'message' => 'Attendance not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $attendance
        ]);
    }

    // 📌 5. update
    public function update(Request $request, $id)
    {
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return response()->json([
                'status' => false,
                'message' => 'Attendance not found'
            ], 404);
        }

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'user_id' => 'required|exists:users,id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:Present,Absent,Late,Permission',
            'remarks' => 'nullable|string'
        ]);

        $attendance->student_id = $request->student_id;
        $attendance->subject_id = $request->subject_id;
        $attendance->user_id = $request->user_id;
        $attendance->attendance_date = $request->attendance_date;
        $attendance->status = $request->status;
        $attendance->remarks = $request->remarks;

        $attendance->save();

        return response()->json([
            'status' => true,
            'message' => 'Attendance updated successfully',
            'data' => $attendance
        ]);
    }

    // 📌 6. destroy
    public function destroy($id)
    {
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return response()->json([
                'status' => false,
                'message' => 'Attendance not found'
            ], 404);
        }

        $attendance->delete();

        return response()->json([
            'status' => true,
            'message' => 'Attendance deleted successfully'
        ]);
    }
}