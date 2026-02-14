<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

use App\Services\ImageKitService;

class HeroSectionController extends Controller
{
    protected $imageKit;

    public function __construct(ImageKitService $imageKit)
    {
        $this->imageKit = $imageKit;
    }

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
        return '<img src="'.$row->background_image.'" alt="Hero BG" style="height:60px; border-radius:6px;">';
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
                $upload = $this->imageKit->upload($request->file('background_image'), 'hero');
                if ($upload) {
                    $data['background_image'] = $upload->url;
                    $data['background_image_file_id'] = $upload->fileId;
                }
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
                $hero = DB::table('hero_section')->where('id', $id)->first();

                // Hapus gambar lama dari ImageKit
                if (!empty($hero->background_image_file_id)) {
                    $this->imageKit->delete($hero->background_image_file_id);
                }

                $upload = $this->imageKit->upload($request->file('background_image'), 'hero');
                if ($upload) {
                    $data['background_image'] = $upload->url;
                    $data['background_image_file_id'] = $upload->fileId;
                }
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

            
             if ($hero->background_image_file_id) {
                $this->imageKit->delete($hero->background_image_file_id);
            }

            DB::table('hero_section')->where('id', $id)->delete();
            return response()->json(['success' => true, 'message' => 'Hero Section deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Error deleting hero section: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => 'Failed to delete hero section: ' . $e->getMessage()], 500);
        }
    }
}
