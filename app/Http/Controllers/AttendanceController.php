<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Student;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendance (សម្រាប់ហៅឈ្មោះតាមថ្នាក់)
     */
// នៅក្នុង StudentController.php
public function index(Request $request)
{
    $classroomId = $request->query('classroom_id');

    $students = Student::with(['classroom', 'subject']) 
        ->where('classroom_id', $classroomId)
        ->get();

    return response()->json([
        'status' => 'success',
        'data' => $students
    ]);
}



public function list(Request $request)
{
    // ត្រូវប្រាកដថាមាន classroom_id បើមិនដូច្នោះទេវានឹងមិនដឹងថាត្រូវបង្ហាញសិស្សថ្នាក់ណាឡើយ
    $classroom_id = $request->classroom_id;

    $studentQuery = \App\Models\Student::with('classroom')
        ->where('classroom_id', $classroom_id);

    // 🔍 ស្វែងរកតាមឈ្មោះ ឬ ID កាត
    if ($request->filled('search')) {
        $search = $request->search;
        $studentQuery->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('student_id_card', 'LIKE', "%{$search}%");
        });
    }

    $students = $studentQuery->paginate(20);
    $studentIds = $students->pluck('id');

    // 📊 គណនាស្ថិតិវត្តមានសម្រាប់សិស្សនីមួយៗ
    $countMap = \App\Models\Attendance::whereIn('student_id', $studentIds)
        // បើមានការជ្រើសរើសថ្ងៃ វានឹងរាប់តែក្នុងថ្ងៃនោះ បើអត់ទេវានឹងរាប់សរុបទាំងអស់
        ->when($request->filled('date'), function ($q) use ($request) {
            $q->where('attendance_date', $request->date);
        })
        ->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status', $request->status);
        })
        ->selectRaw("
            student_id,
            SUM(status = 'Present')    as present,
            SUM(status = 'Absent')     as absent,
            SUM(status = 'Late')       as late,
            SUM(status = 'Permission') as permission
        ")
        ->groupBy('student_id')
        ->get()
        ->keyBy('student_id');

    return response()->json([
        'status'    => 'success',
        'data'      => $students,    // បញ្ជីឈ្មោះសិស្ស (Pagination)
        'count_map' => $countMap,    // ស្ថិតិវត្តមានរបស់សិស្សម្នាក់ៗ
    ]);
}
    

    /**
     * Store new attendance (បញ្ចូលវត្តមាន)
     */

public function store(Request $request)
{
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
        'user_id' => auth()->id(),
    ]);

    return response()->json([
        'status' => true,
        'data' => $attendance
    ]);
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