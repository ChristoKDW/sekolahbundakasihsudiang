<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ParentModel;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        if ($request->filled('education_level')) {
            $query->where('education_level', $request->education_level);
        }

        if ($request->filled('class')) {
            $query->where('class', $request->class);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->with('parents.user')->orderBy('name')->paginate(15)->withQueryString();
        
        $classes = Student::select('class')->distinct()->orderBy('class')->pluck('class');

        return view('admin.students.index', compact('students', 'classes'));
    }

    public function create()
    {
        $parents = \App\Models\ParentModel::with('user')->whereHas('user', function($q) {
            $q->where('is_active', true);
        })->get();
        return view('admin.students.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|string|unique:students,nis',
            'nisn' => 'nullable|string|unique:students,nisn',
            'education_level' => 'required|in:TK,SD,SMP,SMA',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'place_of_birth' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'class' => 'required|string|max:50',
            'major' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive,graduated,dropout',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        $student = Student::create($validated);

        ActivityLog::log('create', 'students', "Created student: {$student->name}");

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function show(Student $student)
    {
        $student->load(['parents.user', 'bills.billType', 'bills.payments']);
        
        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $parents = \App\Models\ParentModel::with('user')->whereHas('user', function($q) {
            $q->where('is_active', true);
        })->get();
        $student->load('parents');
        return view('admin.students.edit', compact('student', 'parents'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'nis' => 'required|string|unique:students,nis,' . $student->id,
            'nisn' => 'nullable|string|unique:students,nisn,' . $student->id,
            'education_level' => 'required|in:TK,SD,SMP,SMA',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'place_of_birth' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'class' => 'required|string|max:50',
            'major' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive,graduated,dropout',
            'photo' => 'nullable|image|max:2048',
        ]);

        $oldData = $student->toArray();

        if ($request->hasFile('photo')) {
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        $student->update($validated);

        ActivityLog::log('update', 'students', "Updated student: {$student->name}", $oldData, $student->toArray());

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $name = $student->name;
        $student->delete();

        ActivityLog::log('delete', 'students', "Deleted student: {$name}");

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }

    public function assignParent(Request $request, Student $student)
    {
        $validated = $request->validate([
            'parent_id' => 'required|exists:parents,id',
        ]);

        $student->parents()->syncWithoutDetaching([$validated['parent_id']]);

        return back()->with('success', 'Orang tua berhasil dihubungkan dengan siswa.');
    }

    public function removeParent(Student $student, ParentModel $parent)
    {
        $student->parents()->detach($parent->id);

        return back()->with('success', 'Hubungan orang tua dengan siswa berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        // TODO: Implement import logic with Laravel Excel or similar

        return back()->with('success', 'Data siswa berhasil diimport.');
    }

    public function export(Request $request)
    {
        // TODO: Implement export logic

        return back()->with('success', 'Data siswa berhasil diexport.');
    }

    public function template()
    {
        // Return a template file for student import
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_siswa.csv"',
        ];

        $columns = ['NIS', 'NISN', 'Nama', 'Jenis Kelamin (L/P)', 'Tempat Lahir', 'Tanggal Lahir (YYYY-MM-DD)', 'Alamat', 'No. Telepon', 'Kelas', 'Jurusan', 'Status (active/inactive)'];
        
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['2024001', '0012345678', 'Contoh Nama Siswa', 'L', 'Makassar', '2010-01-01', 'Jl. Contoh No. 1', '081234567890', 'X-TKJ', 'Teknik Komputer Jaringan', 'active']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
