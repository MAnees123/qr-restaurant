<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff\AttendanceRecord;
use App\Models\Staff\AttendanceSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    private function rid(): int
    {
        return auth()->user()->restaurant_id;
    }

    private function staffQuery()
    {
        return User::where('restaurant_id', $this->rid())->where('role', '!=', 'customer')->orderBy('name');
    }

    // ---- Admin ----

    public function index(Request $request)
    {
        $request->validate(['date' => 'nullable|date']);
        $date = Carbon::parse($request->input('date', today()->toDateString()))->startOfDay();
        $staff = $this->staffQuery()->get();

        $daily = AttendanceRecord::where('restaurant_id', $this->rid())->whereDate('work_date', $date)
            ->get()->keyBy('user_id');

        $monthRecords = AttendanceRecord::where('restaurant_id', $this->rid())
            ->whereBetween('work_date', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])->get()->groupBy('user_id');

        $summary = $staff->mapWithKeys(function ($u) use ($monthRecords) {
            $rows = $monthRecords->get($u->id, collect());
            return [$u->id => [
                'present' => $rows->whereIn('status', ['present', 'late'])->count() + 0.5 * $rows->where('status', 'half_day')->count(),
                'late' => $rows->where('status', 'late')->count(),
                'absent' => $rows->where('status', 'absent')->count(),
                'leave' => $rows->where('status', 'leave')->count(),
                'minutes' => $rows->sum(fn ($r) => $r->workedMinutes()),
            ]];
        });

        return view('staff.attendance.index', [
            'date' => $date, 'staff' => $staff, 'daily' => $daily, 'summary' => $summary,
            'settings' => AttendanceSetting::forRestaurant($this->rid()),
            'statuses' => AttendanceRecord::STATUSES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', Rule::exists('users', 'id')->where('restaurant_id', $this->rid())],
            'work_date' => 'required|date',
            'status' => ['required', Rule::in(AttendanceRecord::STATUSES)],
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i|after:clock_in',
            'note' => 'nullable|string|max:255',
        ]);

        $date = Carbon::parse($data['work_date'])->toDateString();
        $at = fn ($t) => $t ? Carbon::parse("$date $t") : null;

        AttendanceRecord::updateOrCreate(
            ['user_id' => $data['user_id'], 'work_date' => $date],
            [
                'restaurant_id' => $this->rid(),
                'status' => $data['status'],
                'clock_in' => $at($data['clock_in'] ?? null),
                'clock_out' => $at($data['clock_out'] ?? null),
                'note' => $data['note'] ?? null,
            ]
        );

        return redirect()->route('admin.attendance.index', ['date' => $date])->with('success', 'Attendance saved.');
    }

    public function destroy(AttendanceRecord $record)
    {
        abort_unless($record->restaurant_id === $this->rid(), 404);
        $date = $record->work_date->toDateString();
        $record->delete();

        return redirect()->route('admin.attendance.index', ['date' => $date])->with('success', 'Record removed.');
    }

    public function markAbsent(Request $request)
    {
        $data = $request->validate(['date' => 'required|date']);
        $date = Carbon::parse($data['date'])->toDateString();
        $have = AttendanceRecord::where('restaurant_id', $this->rid())->whereDate('work_date', $date)->pluck('user_id');
        $count = 0;

        foreach ($this->staffQuery()->whereNotIn('id', $have)->get() as $u) {
            AttendanceRecord::create(['restaurant_id' => $this->rid(), 'user_id' => $u->id, 'work_date' => $date, 'status' => 'absent']);
            $count++;
        }

        return redirect()->route('admin.attendance.index', ['date' => $date])->with('success', "$count staff marked absent.");
    }

    public function settings(Request $request)
    {
        $data = $request->validate([
            'shift_start' => 'required|date_format:H:i',
            'late_grace_minutes' => 'required|integer|min:0|max:240',
            'standard_hours' => 'required|numeric|min:1|max:24',
        ]);
        AttendanceSetting::forRestaurant($this->rid())->update($data);

        return back()->with('success', 'Attendance settings saved.');
    }

    // ---- Staff self-service ----

    public function my()
    {
        $user = auth()->user();
        $today = AttendanceRecord::where('user_id', $user->id)->whereDate('work_date', today())->first();
        $history = AttendanceRecord::where('user_id', $user->id)->orderByDesc('work_date')->limit(31)->get();

        return view('staff.attendance.my', compact('today', 'history'));
    }

    public function clockIn()
    {
        $user = auth()->user();
        $existing = AttendanceRecord::where('user_id', $user->id)->whereDate('work_date', today())->first();

        if ($existing && $existing->clock_in) {
            return back()->withErrors(['clock' => 'You have already clocked in today.']);
        }

        $s = AttendanceSetting::forRestaurant($user->restaurant_id);
        $limit = today()->setTimeFromTimeString($s->shift_start)->addMinutes($s->late_grace_minutes);
        $status = now()->gt($limit) ? 'late' : 'present';

        AttendanceRecord::updateOrCreate(
            ['user_id' => $user->id, 'work_date' => today()->toDateString()],
            ['restaurant_id' => $user->restaurant_id, 'clock_in' => now(), 'status' => $status]
        );

        return back()->with('success', 'Clocked in' . ($status === 'late' ? ' (marked late).' : '.'));
    }

    public function clockOut()
    {
        $rec = AttendanceRecord::where('user_id', auth()->id())->whereDate('work_date', today())->first();

        if (!$rec || !$rec->clock_in) {
            return back()->withErrors(['clock' => 'You have not clocked in today.']);
        }
        if ($rec->clock_out) {
            return back()->withErrors(['clock' => 'You have already clocked out today.']);
        }

        $rec->update(['clock_out' => now()]);

        return back()->with('success', 'Clocked out.');
    }
}
