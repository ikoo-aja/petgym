<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GymClass;
use App\Models\Trainer;
use App\Models\StaffLog;

class AdminClassController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $classes = GymClass::where('tenant_id', $tenant->id)->with('trainer')->get();
        $trainers = Trainer::where('tenant_id', $tenant->id)->get();

        return view('admin.classes.index', compact('classes', 'trainers', 'tenant'));
    }

    public function storeClass(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $request->validate([
            'name' => 'required|string|max:255',
            'day' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
            'max_capacity' => 'required|integer|min:1',
            'trainer_id' => 'nullable|exists:trainers,id',
        ]);

        $gymClass = GymClass::create([
            'tenant_id' => $tenant->id,
            'name' => $request->name,
            'day' => $request->day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'max_capacity' => $request->max_capacity,
            'trainer_id' => $request->trainer_id,
        ]);

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Tambah Kelas Gym',
            'description' => "Menambahkan kelas baru: {$gymClass->name} pada hari {$gymClass->day}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.classes.index')->with('success', 'Jadwal kelas berhasil ditambahkan.');
    }

    public function updateClass(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $gymClass = GymClass::where('tenant_id', $tenant->id)->findOrFail($id);

        $request->validate([
            'max_capacity' => 'required|integer|min:1',
            'room' => 'required|string|max:255',
            'trainer_id' => 'nullable|exists:trainers,id',
        ]);

        $gymClass->update([
            'max_capacity' => $request->max_capacity,
            'room' => $request->room,
            'trainer_id' => $request->trainer_id,
        ]);

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Alokasi Kelas Gym',
            'description' => "Mengalokasikan kelas {$gymClass->name}: Ruangan {$gymClass->room}, Kuota {$gymClass->max_capacity}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.classes.index')->with('success', 'Alokasi instruktur, ruangan, dan kuota kelas berhasil disimpan.');
    }

    public function storeTrainer(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'specialization' => 'nullable|string',
        ]);

        $trainer = Trainer::create([
            'tenant_id' => $tenant->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'specialization' => $request->specialization,
            'status' => 'active',
        ]);

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Tambah Data Trainer',
            'description' => "Menambahkan trainer baru: {$trainer->name} ({$trainer->specialization})",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.classes.index')->with('success', 'Data trainer berhasil ditambahkan.');
    }

    public function updateTrainer(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $trainer = Trainer::where('tenant_id', $tenant->id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'specialization' => 'nullable|string',
        ]);

        $trainer->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'specialization' => $request->specialization,
        ]);

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Update Data Trainer',
            'description' => "Memperbarui data trainer: {$trainer->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.classes.index')->with('success', 'Data trainer berhasil diperbarui.');
    }

    public function destroyTrainer(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $trainer = Trainer::where('tenant_id', $tenant->id)->findOrFail($id);
        $trainer->delete();

        return redirect()->route('admin.classes.index')->with('success', 'Data trainer berhasil dihapus.');
    }
}
