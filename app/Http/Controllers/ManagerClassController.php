<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GymClass;
use App\Models\Trainer;
use App\Models\StaffLog;
use App\Models\Tenant;

class ManagerClassController extends Controller
{
    private function getTenant()
    {
        $user = Auth::user();
        if ($user && $user->tenant) {
            return $user->tenant;
        }
        abort(403, 'Anda belum memiliki tenant atau website gym yang aktif.');
    }

    public function index()
    {
        $user = Auth::user();
        $tenant = $this->getTenant();
        $classes = GymClass::where('tenant_id', $tenant->id)->with('trainer')->get();
        $trainers = Trainer::where('tenant_id', $tenant->id)->get();
        return view('manager.classes.index', compact('classes', 'trainers', 'tenant'));
    }

    public function storeClass(Request $request)
    {
        $user = Auth::user();
        if ($user && !$user->isManager()) {
            return redirect()->back()->with('error', 'Pengaturan jadwal kelas & trainer dikelola oleh Manager Gym.');
        }
        $tenant = $this->getTenant();
        $request->validate([
            'name' => 'required|string|max:255',
            'day' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
            'max_capacity' => 'required|integer|min:1',
            'room' => 'nullable|string|max:255',
            'trainer_id' => 'nullable|exists:trainers,id',
        ]);
        $gymClass = GymClass::create([
            'tenant_id' => $tenant->id,
            'name' => $request->name,
            'day' => $request->day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'max_capacity' => $request->max_capacity,
            'room' => $request->room ?? 'Studio Utama',
            'trainer_id' => $request->trainer_id,
        ]);
        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user ? $user->id : null,
            'action' => 'Tambah Kelas Gym',
            'description' => "Menambahkan kelas baru: {$gymClass->name} pada hari {$gymClass->day}",
            'ip_address' => $request->ip(),
        ]);
        return redirect()->route('manager.classes.index')->with('success', 'Jadwal kelas baru berhasil ditambahkan ke database.');
    }

    public function updateClass(Request $request, $id)
    {
        $user = Auth::user();
        if ($user && !$user->isManager()) {
            return redirect()->back()->with('error', 'Pengaturan jadwal kelas & trainer dikelola oleh Manager Gym.');
        }
        $tenant = $this->getTenant();
        $gymClass = GymClass::where('tenant_id', $tenant->id)->findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'day' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
            'max_capacity' => 'required|integer|min:1',
            'room' => 'nullable|string|max:255',
            'trainer_id' => 'nullable|exists:trainers,id',
        ]);
        $gymClass->update([
            'name' => $request->name,
            'day' => $request->day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'max_capacity' => $request->max_capacity,
            'room' => $request->room ?? 'Studio Utama',
            'trainer_id' => $request->trainer_id,
        ]);
        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user ? $user->id : null,
            'action' => 'Edit Kelas Gym',
            'description' => "Memperbarui kelas {$gymClass->name}: Hari {$gymClass->day}, Ruangan {$gymClass->room}, Kuota {$gymClass->max_capacity}",
            'ip_address' => $request->ip(),
        ]);
        return redirect()->route('manager.classes.index')->with('success', 'Jadwal kelas berhasil diperbarui di database.');
    }

    public function destroyClass(Request $request, $id)
    {
        $user = Auth::user();
        if ($user && !$user->isManager()) {
            return redirect()->back()->with('error', 'Pengaturan jadwal kelas & trainer dikelola oleh Manager Gym.');
        }
        $tenant = $this->getTenant();
        $gymClass = GymClass::where('tenant_id', $tenant->id)->findOrFail($id);
        $className = $gymClass->name;
        $gymClass->delete();
        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user ? $user->id : null,
            'action' => 'Hapus Kelas Gym',
            'description' => "Menghapus kelas gym: {$className}",
            'ip_address' => $request->ip(),
        ]);
        return redirect()->route('manager.classes.index')->with('success', "Jadwal kelas '{$className}' berhasil dihapus dari database.");
    }

    public function storeTrainer(Request $request)
    {
        $user = Auth::user();
        if ($user && !$user->isManager()) {
            return redirect()->back()->with('error', 'Pengaturan jadwal kelas & trainer dikelola oleh Manager Gym.');
        }
        $tenant = $this->getTenant();
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
            'user_id' => $user ? $user->id : null,
            'action' => 'Tambah Data Trainer',
            'description' => "Menambahkan trainer baru: {$trainer->name} ({$trainer->specialization})",
            'ip_address' => $request->ip(),
        ]);
        return redirect()->route('manager.classes.index')->with('success', "Trainer '{$trainer->name}' berhasil disimpan ke database!");
    }

    public function updateTrainer(Request $request, $id)
    {
        $user = Auth::user();
        if ($user && !$user->isManager()) {
            return redirect()->back()->with('error', 'Pengaturan jadwal kelas & trainer dikelola oleh Manager Gym.');
        }
        $tenant = $this->getTenant();
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
            'user_id' => $user ? $user->id : null,
            'action' => 'Update Data Trainer',
            'description' => "Memperbarui data trainer: {$trainer->name}",
            'ip_address' => $request->ip(),
        ]);
        return redirect()->route('manager.classes.index')->with('success', "Data trainer '{$trainer->name}' berhasil diperbarui di database.");
    }

    public function destroyTrainer(Request $request, $id)
    {
        $user = Auth::user();
        if ($user && !$user->isManager()) {
            return redirect()->back()->with('error', 'Pengaturan jadwal kelas & trainer dikelola oleh Manager Gym.');
        }
        $tenant = $this->getTenant();
        $trainer = Trainer::where('tenant_id', $tenant->id)->findOrFail($id);
        $trainerName = $trainer->name;
        $trainer->delete();
        return redirect()->route('manager.classes.index')->with('success', "Data trainer '{$trainerName}' berhasil dihapus dari database.");
    }
}
