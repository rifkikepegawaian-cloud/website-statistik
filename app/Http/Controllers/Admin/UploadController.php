<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Upload;

class UploadController extends Controller
{
    /**
     * Beranda publik: pilih data terbaru & daftar tanggal.
     */
    public function home()
    {
        $uploads  = Upload::orderByDesc('uploaded_at')->get();
        $selected = $uploads->first();

        return view('home', compact('uploads', 'selected'));
    }

    /**
     * API publik untuk statistik agregat (bukan baris mentah).
     */
    public function stats(Upload $upload)
    {
        return response()->json($upload->stats);
    }

    /**
     * Dashboard (user & admin bisa masuk).
     */
    public function dashboard()
    {
        return view('admin.index');
    }

    /**
     * Riwayat upload (user & admin).
     */
    public function index()
    {
        $this->authorize('uploads.view');

        $uploads = Upload::orderByDesc('uploaded_at')->get();
        return view('admin.uploads.index', compact('uploads'));
    }

    /**
     * Form upload (admin only).
     */
    public function create()
    {
        $this->authorize('uploads.create');

        return view('admin.uploads.create');
    }

    /**
     * Simpan upload (admin only).
     * File disimpan ke disk 'private' agar tidak bisa diakses langsung.
     */
    public function store(Request $request)
    {
        $this->authorize('uploads.create');

        $request->validate([
            'file'         => 'required|file|mimes:xlsx,xls,csv|max:20480', // 20MB
            'stats_json'   => 'required|string',
            'row_count'    => 'required|integer|min:0',
            'preview_json' => 'required|string',
            'uploaded_at'  => 'nullable|date',
        ]);

        $file       = $request->file('file');
        $origName   = $file->getClientOriginalName();
        $ext        = $file->getClientOriginalExtension();
        $storedName = Str::uuid()->toString().'.'.strtolower($ext);

        // Simpan ke storage privat
        $path = Storage::disk('local')->putFileAs('uploads', $file, $storedName);

        Upload::create([
            'original_name' => $origName,
            'file_path'     => $path, // contoh: uploads/xxxx-uuid.xlsx
            'row_count'     => (int) $request->row_count,
            'stats_json'    => $request->stats_json,
            'preview_json'  => $request->preview_json,
            'uploaded_at'   => $request->uploaded_at
                                ? Carbon::parse($request->uploaded_at)
                                : now(),
        ]);

        return redirect()->route('admin.uploads.index')->with('status', 'Data berhasil disimpan.');
    }

    /**
     * Detail (user & admin).
     */
    public function show(Upload $upload)
    {
        $this->authorize('uploads.view');

        $preview = $upload->preview;
        return view('admin.uploads.show', compact('upload', 'preview'));
    }

    /**
     * Unduh file asli (user & admin) melalui storage privat.
     */
    public function download(Upload $upload)
    {
        $this->authorize('uploads.view');

        $disk = Storage::disk('local');
        if (!$disk->exists($upload->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $fullPath = $disk->path($upload->file_path); // storage/app/private/...
        return response()->download($fullPath, $upload->original_name);
    }

    /**
     * Hapus data (admin only).
     */
    public function destroy(Upload $upload)
    {
        $this->authorize('uploads.delete');

        try {
            if ($upload->file_path && Storage::disk('local')->exists($upload->file_path)) {
                Storage::disk('local')->delete($upload->file_path);
            }
        } catch (\Throwable $e) {
            // log kalau perlu
        }

        $upload->delete();

        return back()->with('status', 'Data berhasil dihapus.');
    }

    /**
     * (Opsional) Halaman edit terpisah jika diperlukan (kamu pakai modal di index).
     */
    public function edit(Upload $upload)
    {
        $this->authorize('uploads.update');

        return view('admin.uploads.edit', compact('upload'));
    }

    /**
     * Update tanggal upload (admin only).
     */
    public function update(Request $request, $id)
    {
        $this->authorize('uploads.update'); // ← FIX: gunakan gate yang benar

        $request->validate([
            'uploaded_at' => 'required|date',
        ]);

        $upload = Upload::findOrFail($id);
        $upload->uploaded_at = Carbon::parse($request->uploaded_at);
        $upload->save();

        return redirect()->route('admin.uploads.index')->with('status', 'Tanggal upload berhasil diubah!');
    }
}
