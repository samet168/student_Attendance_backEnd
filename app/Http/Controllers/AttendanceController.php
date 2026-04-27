<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendance (សម្រាប់ហៅឈ្មោះតាមថ្នាក់)
     */
    public function index(Request $request)
    {
        $data = Attendance::with(['student', 'classroom'])
            ->where('classroom_id', $request->classroom_id)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    /**
     * Store new attendance (បញ្ចូលវត្តមាន)
     */
public function store(Request $request)
{
    try {
        $request->validate([
            'student_id' => 'required',
            'classroom_id' => 'required',
            'subject_id' => 'required',
            'attendance_date' => 'required',
            'status' => 'required',
        ]);

        $attendance = Attendance::create([
            'student_id' => $request->student_id,
            'classroom_id' => $request->classroom_id,
            'subject_id' => $request->subject_id,
            'attendance_date' => $request->attendance_date,
            'status' => $request->status,
            'remarks' => $request->remarks,
            'user_id' => auth()->id(), // 🔥 IMPORTANT
        ]);

        return response()->json([
            'status' => true,
            'data' => $attendance
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Show single attendance
     */
    public function show($id)
    {
        $attendance = Attendance::with(['student', 'classroom', 'user'])->find($id);

        if (!$attendance) {
            return response()->json([
                'status'  => false,
                'message' => 'Attendance not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $attendance
        ]);
    }

    /**
     * Update attendance
     */
    public function update(Request $request, $id)
    {
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return response()->json([
                'status'  => false,
                'message' => 'Attendance not found'
            ], 404);
        }

        $request->validate([
            'student_id'      => 'required|exists:students,id',
            'classroom_id'    => 'required|exists:classrooms,id',
            'user_id'         => 'required|exists:users,id',
            'attendance_date' => 'required|date',
            'status'          => 'required|in:Present,Absent,Late,Permission',
            'remarks'         => 'nullable|string|max:500',
            'time_in'         => 'nullable|date_format:H:i',
            'time_out'        => 'nullable|date_format:H:i',
        ]);

        $attendance->update([
            'student_id'      => $request->student_id,
            'classroom_id'    => $request->classroom_id,
            'user_id'         => $request->user_id,
            'attendance_date' => $request->attendance_date,
            'status'          => $request->status,
            'remarks'         => $request->remarks,
            'time_in'         => $request->time_in,
            'time_out'        => $request->time_out,
        ]);

        $attendance->load(['student', 'classroom', 'user']);

        return response()->json([
            'status'  => true,
            'message' => 'Attendance updated successfully',
            'data'    => $attendance
        ]);
    }

    /**
     * Delete attendance
     */
    public function destroy($id)
    {
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return response()->json([
                'status'  => false,
                'message' => 'Attendance not found'
            ], 404);
        }

        $attendance->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Attendance deleted successfully'
        ]);
    }

    /**
     * List with pagination + search (បើត្រូវការ)
     */
    public function list(Request $request)
    {
        $query = Attendance::with(['student', 'classroom', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('classroom', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $attendances = $query->latest('attendance_date')->paginate(15);

        return response()->json([
            'status'  => true,
            'message' => 'Attendance list',
            'data'    => $attendances
        ]);
    }

public function countPerStudent(Request $request)
{
    if (!$request->classroom_id) {
        return response()->json([
            'status' => false,
            'message' => 'classroom_id required'
        ]);
    }

    $data = Attendance::where('classroom_id', $request->classroom_id)->get();

    if ($data->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No attendance found for this classroom'
        ]);
    }

    return response()->json([
        'status' => true,
        'data' => $data
    ]);
}
}