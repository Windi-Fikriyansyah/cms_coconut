<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use Yajra\DataTables\Facades\DataTables;

class HeroSectionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('hero_section')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('hero.edit', $row->id);
                    $deleteUrl = route('hero.destroy', $row->id);
                    $btn = '<div class="btn-group" role="group">';
                    $btn .= '<a href="' . $editUrl . '" class="btn btn-sm btn-info me-1" title="Edit"><i class="bi bi-pencil-square"></i></a>';
                    $btn .= '<button type="button" class="btn btn-sm btn-danger delete-btn" data-url="' . $deleteUrl . '" title="Hapus"><i class="bi bi-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->addColumn('background_image', function ($row) {
    if ($row->background_image) {

        // jika path sudah /uploads/nama.jpg
        $url = Storage::disk('nextjs')->url(
            str_replace('/uploads/', '', $row->background_image)
        );

        return '<img src="'.$url.'" alt="Hero BG" style="height:60px; border-radius:6px;">';
    }
    return 'No Image';
})

                ->rawColumns(['action', 'background_image'])
                ->make(true);
        }

        return view('hero_section.index');
    }

    public function create()
    {
        return view('hero_section.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'badge_text' => 'required|string',
            'title' => 'required|string',
            'subtitle' => 'required|string',
            'cta_text' => 'required|string',
            'cta_link' => 'nullable|string',
            'background_image' => 'nullable|file|mimes:webp|mimetypes:image/webp|max:2048',
        ]);
        
        


        try {
            $data = [
                'badge_text' => $request->badge_text,
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'cta_text' => $request->cta_text,
                'cta_link' => $request->cta_link,
            ];

            if ($request->hasFile('background_image')) {
    $image = $request->file('background_image');
    
    // Generate nama file unik
    $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
    
    // Simpan ke disk 'nextjs' (folder public/uploads di Next.js)
    // storeAs parameter: (path_tujuan, nama_file, nama_disk)
    Storage::disk('nextjs')->putFileAs('', $image, $imageName);
    
    // Simpan path relatif ke database agar bisa dipanggil di Next.js
    $data['background_image'] = '/uploads/' . $imageName;
}

            DB::table('hero_section')->insert($data);

            return redirect()->route('hero.index')->with('success', 'Hero Section created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating hero section: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create hero section: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $heroSection = DB::table('hero_section')->where('id', $id)->first();

        if (!$heroSection) {
            return redirect()->route('hero.index')->with('error', 'Hero Section not found.');
        }

        return view('hero_section.edit', compact('heroSection'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'badge_text' => 'required|string',
            'title' => 'required|string',
            'subtitle' => 'required|string',
            'cta_text' => 'required|string',
            'cta_link' => 'required|string',
            'background_image' => 'nullable|file|mimes:webp|mimetypes:image/webp|max:2048',

        ]);
        
        

        try {
            $data = [
                'badge_text' => $request->badge_text,
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'cta_text' => $request->cta_text,
                'cta_link' => $request->cta_link,
            ];

            if ($request->hasFile('background_image')) {

    // ambil data lama SEKALI saja (lebih efisien)
    $hero = DB::table('hero_section')->where('id', $id)->first();

    // 🔥 Hapus gambar lama jika ada
    if (!empty($hero->background_image)) {

        // ambil nama file saja (anti salah path)
        $oldFile = basename($hero->background_image);

        // cek dulu apakah file benar ada
        if (Storage::disk('nextjs')->exists($oldFile)) {
            Storage::disk('nextjs')->delete($oldFile);
        }
    }

    // Upload gambar baru
    $image = $request->file('background_image');

    $imageName = time().'_'.Str::random(10).'.'.$image->getClientOriginalExtension();

    Storage::disk('nextjs')->putFileAs('', $image, $imageName);

    $data['background_image'] = '/uploads/'.$imageName;
}


            DB::table('hero_section')->where('id', $id)->update($data);

            return redirect()->route('hero.index')->with('success', 'Hero Section updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating hero section: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update hero section: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $hero = DB::table('hero_section')->where('id', $id)->first();
            
            if (!$hero) {
                return response()->json(['error' => true, 'message' => 'Hero Section not found.'], 404);
            }

            
             if ($hero->background_image) {
                $oldFile = str_replace('uploads/', '', $hero->background_image);
                Storage::disk('nextjs')->delete($oldFile);
            }

            DB::table('hero_section')->where('id', $id)->delete();
            return response()->json(['success' => true, 'message' => 'Hero Section deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Error deleting hero section: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => 'Failed to delete hero section: ' . $e->getMessage()], 500);
        }
    }
}
