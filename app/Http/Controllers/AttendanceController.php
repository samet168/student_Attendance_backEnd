<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use MongoDB\BSON\ObjectId;



class AttendanceController extends Controller
{


// public function list(Request $request)
// {
//     try {

//         if (!$request->classroom_id) {
//             return response()->json([
//                 'status' => false,
//                 'message' => 'classroom_id required'
//             ], 400);
//         }

//         $classroomId = $request->classroom_id;

//         $page  = max((int)$request->page, 1);
//         $limit = 10;
//         $skip  = ($page - 1) * $limit;

//         // =====================
//         // 1. GET STUDENTS
//         // =====================
//         $studentsQuery = Student::where('classroom_id', $classroomId);

//         $students = $studentsQuery
//             ->skip($skip)
//             ->limit($limit)
//             ->get();

//         $total = $studentsQuery->count();

//         // =====================
//         // 2. GET ATTENDANCE
//         // =====================
//         $attendances = Attendance::where('classroom_id', $classroomId)->get();

//         // =====================
//         // 3. COUNT MAP
//         // =====================
//         $countMap = [];

//         foreach ($attendances as $att) {

//             $sid = (string)$att->student_id;

//             if (!isset($countMap[$sid])) {
//                 $countMap[$sid] = [
//                     'present' => 0,
//                     'absent' => 0,
//                     'late' => 0,
//                     'permission' => 0,
//                 ];
//             }

//             switch (strtolower($att->status)) {
//                 case 'present':
//                     $countMap[$sid]['present']++;
//                     break;
//                 case 'absent':
//                     $countMap[$sid]['absent']++;
//                     break;
//                 case 'late':
//                     $countMap[$sid]['late']++;
//                     break;
//                 case 'permission':
//                     $countMap[$sid]['permission']++;
//                     break;
//             }
//         }

//         // =====================
//         // 4. DEBUG (IMPORTANT)
//         // =====================
//         if ($students->isEmpty()) {
//             return response()->json([
//                 'status' => false,
//                 'message' => 'No students found for this classroom',
//                 'debug_classroom_id' => $classroomId
//             ]);
//         }

//         return response()->json([
//             'status' => true,
//             'data' => [
//                 'data' => $students,
//                 'current_page' => $page,
//                 'per_page' => $limit,
//                 'total' => $total,
//                 'last_page' => ceil($total / $limit),
//             ],
//             'count_map' => $countMap
//         ]);

//     } catch (\Exception $e) {
//         return response()->json([
//             'status' => false,
//             'message' => $e->getMessage()
//         ], 500);
//     }
// }
public function list(Request $request)
{
    try {

        if (!$request->classroom_id) {
            return response()->json([
                'status' => false,
                'message' => 'classroom_id required'
            ], 400);
        }

        $user = Auth::user();

        $classroomId = $request->classroom_id;

        $page  = max((int)$request->page, 1);
        $limit = 10;
        $skip  = ($page - 1) * $limit;

        // =====================
        // STUDENTS
        // =====================
        $studentsQuery = Student::where('classroom_id', $classroomId);

        $students = $studentsQuery
            ->skip($skip)
            ->limit($limit)
            ->get();

        $total = $studentsQuery->count();

        // =====================
        // ATTENDANCE QUERY (FIX HERE)
        // =====================
        $attendanceQuery = Attendance::where('classroom_id', $classroomId);

        // 👨‍🏫 teacher only sees own data
        if ($user->role !== 'admin') {
            $attendanceQuery->where('user_id', $user->id);
        }

        // 📘 filter by subject
        if ($request->subject_id) {
            $attendanceQuery->where('subject_id', $request->subject_id);
        }

        // 📅 filter by date
        if ($request->date) {
            $start = Carbon::parse($request->date)->startOfDay();
            $end   = Carbon::parse($request->date)->endOfDay();

            $attendanceQuery->whereBetween('attendance_date', [$start, $end]);
        }

        // 🔍 filter by status
        if ($request->status) {
            $attendanceQuery->where('status', $request->status);
        }

        $attendances = $attendanceQuery->get();

        // =====================
        // COUNT MAP
        // =====================
        $countMap = [];

        foreach ($attendances as $att) {

            $sid = (string)$att->student_id;

            if (!isset($countMap[$sid])) {
                $countMap[$sid] = [
                    'present' => 0,
                    'absent' => 0,
                    'late' => 0,
                    'permission' => 0,
                ];
            }

            switch (strtolower($att->status)) {
                case 'present':
                    $countMap[$sid]['present']++;
                    break;
                case 'absent':
                    $countMap[$sid]['absent']++;
                    break;
                case 'late':
                    $countMap[$sid]['late']++;
                    break;
                case 'permission':
                    $countMap[$sid]['permission']++;
                    break;
            }
        }

        // =====================
        // RESPONSE
        // =====================
        return response()->json([
            'status' => true,
            'data' => [
                'data' => $students,
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'last_page' => ceil($total / $limit),
            ],
            'count_map' => $countMap
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);
    }
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
// public function store(Request $request)
// {
//     $request->validate([
//         'student_id' => 'required',
//         'classroom_id' => 'required',
//         'subject_id' => 'required',
//         'attendance_date' => 'required|date',
//         'status' => 'required|in:present,absent,late,permission',
//     ]);

//     $attendance = Attendance::create([
//         'student_id' => $request->student_id,
//         'classroom_id' => $request->classroom_id,
//         'subject_id' => $request->subject_id,
//         'attendance_date' => $request->attendance_date,
//         'status' => $request->status,
//         'remarks' => $request->remarks ?? "",
//         'user_id' => auth()->id() ?? 1, // ✅ FIX (no login)
//     ]);

//     return response()->json([
//         'status' => true,
//         'data' => $attendance
//     ]);
// }
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