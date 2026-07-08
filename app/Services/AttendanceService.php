<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    /**
     * Get or generate attendance for a specific date
     */
    public function getAttendanceForDate($date, $search = null)
    {
        $dateStr = Carbon::parse($date)->format('Y-m-d');
        
        // Ensure attendance exists for all active users for this date
        $this->generateMissingAttendance($dateStr);
        
        $query = Attendance::with('user:id,name,team')
            ->where('date', $dateStr);
            
        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        
        $attendances = $query->get();
            
        // Group by team
        $grouped = $attendances->groupBy(function($attendance) {
            return $attendance->user->team ?? 'Unassigned';
        });
            
        return [
            'grouped' => $grouped,
            'summary' => [
                'total' => Attendance::where('date', $dateStr)->count(),
                'present' => Attendance::where('date', $dateStr)
                                ->whereIn('status', ['P', 'S', 'BT'])->count(),
                'leave' => Attendance::where('date', $dateStr)
                                ->whereIn('status', ['AL', 'AL 1/2', 'SL', 'CL', 'CD'])->count(),
                'absent' => Attendance::where('date', $dateStr)
                                ->whereIn('status', ['A', 'UL'])->count(),
                'empty' => Attendance::where('date', $dateStr)
                                ->where(function($q) {
                                    $q->whereNull('status')->orWhere('status', '')->orWhere('status', ' ');
                                })->count()
            ]
        ];
    }
    
    /**
     * Generate missing attendance records for the given date
     */
    public function generateMissingAttendance($dateStr)
    {
        // Find users that don't have an attendance record for this date
        $existingUserIds = Attendance::where('date', $dateStr)->pluck('user_id')->toArray();
        
        // Select only required columns (complying with agent.md: avoid SELECT *)
        $missingUsers = User::select('id')
            ->where('status', 'Active')
            ->whereNotIn('id', $existingUserIds)
            ->get();
            
        if ($missingUsers->isEmpty()) {
            return;
        }

        $insertData = [];
        $now = now();
        foreach ($missingUsers as $user) {
            $insertData[] = [
                'user_id' => $user->id,
                'date' => $dateStr,
                'status' => ' ',
                'verify' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        
        // Chunk insert
        foreach(array_chunk($insertData, 500) as $chunk) {
            Attendance::insert($chunk);
        }
    }
    
    /**
     * Update attendance status
     */
    public function updateStatus($id, $status)
    {
        return Attendance::where('id', $id)->update(['status' => $status]);
    }
    
    /**
     * Update all users in a specific section/team to a specific status for a given date
     * where their current status is empty
     */
    public function updateSectionStatus($team, $date, $status = 'P')
    {
        $dateStr = Carbon::parse($date)->format('Y-m-d');
        
        // Find users in this team
        $userIds = User::where('team', $team)->pluck('id');
        
        // Update their attendance
        Attendance::whereIn('user_id', $userIds)
            ->where('date', $dateStr)
            ->where(function($q) {
                $q->whereNull('status')
                  ->orWhere('status', '')
                  ->orWhere('status', ' ');
            })
            ->update(['status' => $status]);
    }

    /**
     * Update all users to a specific status for a given date
     * where their current status is empty
     */
    public function updateAllStatus($date, $status = 'P')
    {
        $dateStr = Carbon::parse($date)->format('Y-m-d');
        
        Attendance::where('date', $dateStr)
            ->where(function($q) {
                $q->whereNull('status')
                  ->orWhere('status', '')
                  ->orWhere('status', ' ');
            })
            ->update(['status' => $status]);
    }
}
