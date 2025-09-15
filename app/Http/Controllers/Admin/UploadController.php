<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Upload;

class UploadController extends Controller
{
    public function home()
    {
        $uploads = Upload::orderByDesc('uploaded_at')->get();
        $selected = $uploads->first();
        return view('home', compact('uploads', 'selected'));
    }

    public function stats(Upload $upload)
    {
        return response()->json($upload->stats);
    }

    public function dashboard()
    {
        return view('admin.index');
    }

    public function index()
    {
        $uploads = Upload::orderByDesc('uploaded_at')->get();
        return view('admin.uploads.index', compact('uploads'));
    }

    public function create()
    {
        return view('admin.uploads.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'stats_json' => 'required|string',
            'row_count' => 'required|integer|min:0',
            'preview_json' => 'required|string',
        ]);

        $file = $request->file('file');
        $path = $file->store('uploads');

        $upload = Upload::create([
            'original_name' => $file->getClientOriginalName(),
            'file_path'     => $path,
            'row_count'     => (int)$request->row_count,
            'stats_json'    => $request->stats_json,
            'preview_json'  => $request->preview_json,
            'uploaded_at'   => now(),
        ]);

        return redirect()->route('uploads.index')->with('status','Data berhasil disimpan.');
    }

    public function show(Upload $upload)
    {
        $preview = $upload->preview;
        return view('admin.uploads.show', compact('upload','preview'));
    }

    public function download(Upload $upload)
    {
        return Storage::download($upload->file_path, $upload->original_name);
    }

    public function destroy(Upload $upload)
    {
        Storage::delete($upload->file_path);
        $upload->delete();
        return back()->with('status','Data berhasil dihapus.');
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'uploaded_at' => 'required|date',
        ]);

        $upload = \App\Models\Upload::findOrFail($id);
        $upload->uploaded_at = $request->uploaded_at;
        $upload->save();

        return redirect()->route('uploads.index')->with('status', 'Tanggal upload berhasil diubah!');
    }
}
