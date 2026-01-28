<?php

namespace App\Http\Controllers\Parents;

use App\Http\Controllers\Controller;
use App\Models\ParentModel;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $parent = $user->parentProfile;
        
        if (!$parent) {
            $parent = ParentModel::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'relationship' => 'wali',
                'phone' => $user->phone ?? '',
                'address' => '-',
            ]);
        }

        $parent->load('students');

        return view('parent.profile.index', compact('user', 'parent'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $parent = $user->parentProfile;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'relationship' => 'required|in:ayah,ibu,wali',
            'occupation' => 'nullable|string|max:255',
            'address' => 'required|string',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);

        $parent->update([
            'name' => $validated['name'],
            'relationship' => $validated['relationship'],
            'occupation' => $validated['occupation'] ?? null,
            'phone' => $validated['phone'],
            'address' => $validated['address'],
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|confirmed|min:8',
        ]);

        Auth::user()->update([
            'password' => bcrypt($validated['password']),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }

    public function students()
    {
        $user = Auth::user();
        $parent = $user->parentProfile;
        
        $students = $parent ? $parent->students()->with('bills')->get() : collect();

        return view('parent.students.index', compact('students'));
    }

    public function showStudent(Student $student)
    {
        $user = Auth::user();
        $parent = $user->parentProfile;

        if (!$parent || !$parent->students->contains($student)) {
            abort(403);
        }

        $student->load(['bills.billType', 'bills.payments']);

        return view('parent.students.show', compact('student'));
    }
}
