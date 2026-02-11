<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class GalleryController extends Controller
{
    /**
     * INDEX
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = DB::table('gallery_metadata')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    if (!$row->background_image) return 'No Image';
                    $url = Storage::disk('nextjs')->url(str_replace('/uploads/', '', $row->background_image));
                    return '<img src="' . $url . '" style="height:50px; border-radius:6px;">';
                })
                ->addColumn('images_count', function ($row) {
                    return DB::table('gallery_images')
                        ->where('gallery_metadata_id', $row->id)
                        ->count();
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="btn-group">
                            <a href="'.route('gallery.edit', $row->id).'" class="btn btn-sm btn-info">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <button class="btn btn-sm btn-danger delete-btn" data-url="'.route('gallery.destroy', $row->id).'">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['image', 'action'])
                ->make(true);
        }

        // Check if data exists for "Add" button visibility logic (Singleton pattern)
        $count = DB::table('gallery_metadata')->count();
        return view('gallery.index', compact('count'));
    }

    /**
     * CREATE
     */
    public function create()
    {
        return view('gallery.create');
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'             => 'required|string',
            'subtitle'          => 'required|string',
            'description'       => 'nullable|string',
            'background_image'  => 'required|image|mimes:webp|max:2048',
            
            // Gallery Images
            'gallery_images'    => 'nullable|array',
            'gallery_images.*'  => 'nullable|image|mimes:webp|max:2048',
            'img_title'         => 'nullable|array',
            'img_title.*'       => 'nullable|string',
            'img_category'      => 'nullable|array',
            'img_category.*'    => 'nullable|string',
            'img_display_order' => 'nullable|array',
            'img_display_order.*'=> 'nullable|integer',
        ]);

        try {
            // 1. Upload Background Image
            $bgPath = null;
            if ($request->hasFile('background_image')) {
                $file = $request->file('background_image');
                $name = time().'_bg_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                Storage::disk('nextjs')->putFileAs('', $file, $name);
                $bgPath = '/uploads/'.$name;
            }

            // 2. Insert Metadata
            $metaId = DB::table('gallery_metadata')->insertGetId([
                'title'            => $request->title,
                'subtitle'         => $request->subtitle,
                'description'      => $request->description,
                'background_image' => $bgPath,
            ]);

            // 3. Insert Gallery Images
            if ($request->img_title) {
                foreach ($request->img_title as $idx => $title) {
                    // Upload image for this item if exists
                    $srcPath = null;
                    if ($request->hasFile("gallery_images.$idx")) {
                        $f = $request->file("gallery_images")[$idx]; // standard array access for files
                        $fname = time().'_img_'.Str::random(8).'.'.$f->getClientOriginalExtension();
                        Storage::disk('nextjs')->putFileAs('', $f, $fname);
                        $srcPath = '/uploads/'.$fname;
                    }

                    // Only insert if title OR image is provided (or just image? prompt says src required logic usually)
                    // But standard logic: skip empty rows. 
                    if (!$srcPath && !$title) continue;

                    DB::table('gallery_images')->insert([
                        'gallery_metadata_id' => $metaId,
                        'src'           => $srcPath ?? '',
                        'title'         => $title ?? '',
                        'category'      => $request->img_category[$idx] ?? '',
                        'display_order' => $request->img_display_order[$idx] ?? 0,
                    ]);
                }
            }

            return redirect()->route('gallery.index')->with('success', 'Gallery created successfully');

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed create gallery: ' . $e->getMessage());
        }
    }

    /**
     * EDIT
     */
    public function edit($id)
    {
        $metadata = DB::table('gallery_metadata')->where('id', $id)->first();
        $images = DB::table('gallery_images')
            ->where('gallery_metadata_id', $id)
            ->orderBy('display_order')
            ->get();

        return view('gallery.edit', compact('metadata', 'images'));
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title'             => 'required|string',
            'subtitle'          => 'required|string',
            'description'       => 'nullable|string',
            'background_image'  => 'nullable|image|mimes:webp|max:2048',

             // Gallery Images
            'img_id'            => 'nullable|array',
            'gallery_images'    => 'nullable|array',
            'gallery_images.*'  => 'nullable|image|mimes:webp|max:2048',
            'img_title'         => 'nullable|array',
            'img_title.*'       => 'nullable|string',
            'img_category'      => 'nullable|array',
            'img_category.*'    => 'nullable|string',
            'img_display_order' => 'nullable|array',
            'img_display_order.*'=> 'nullable|integer',
        ]);

        try {
            $metadata = DB::table('gallery_metadata')->where('id', $id)->first();

            // 1. Update Metadata
            $updateData = [
                'title'       => $request->title,
                'subtitle'    => $request->subtitle,
                'description' => $request->description,
            ];

            if ($request->hasFile('background_image')) {
                // Delete old bg
                if ($metadata->background_image) {
                    $oldBg = basename($metadata->background_image);
                    if (Storage::disk('nextjs')->exists($oldBg)) {
                        Storage::disk('nextjs')->delete($oldBg);
                    }
                }
                // Upload new bg
                $file = $request->file('background_image');
                $name = time().'_bg_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                Storage::disk('nextjs')->putFileAs('', $file, $name);
                $updateData['background_image'] = '/uploads/'.$name;
            }

            DB::table('gallery_metadata')->where('id', $id)->update($updateData);

            // 2. Sync Gallery Images
            $existingIds = DB::table('gallery_images')->where('gallery_metadata_id', $id)->pluck('id')->toArray();
            $submittedIds = array_filter($request->img_id ?? []);

            // Delete removed items
            $toDelete = array_diff($existingIds, $submittedIds);
            foreach ($toDelete as $delId) {
                $imgRow = DB::table('gallery_images')->where('id', $delId)->first();
                if ($imgRow && $imgRow->src) {
                    $f = basename($imgRow->src);
                    if (Storage::disk('nextjs')->exists($f)) {
                        Storage::disk('nextjs')->delete($f);
                    }
                }
                DB::table('gallery_images')->where('id', $delId)->delete();
            }

            // Update or Insert items
            if ($request->img_title) {
                foreach ($request->img_title as $idx => $title) {
                    $itemId = $request->img_id[$idx] ?? null;
                    
                    // Logic to handle file upload for this item
                    // Note: file inputs in loop with same name array
                    // <input type="file" name="gallery_images[idx]">
                    // In Laravel $request->file('gallery_images') is array keyed by index if formulated correctly.
                    
                    $srcPath = null;
                    if ($request->hasFile("gallery_images.$idx")) {
                        $f = $request->file("gallery_images")[$idx]; 
                        // Actually if we use name="gallery_images[0]" then request->file('gallery_images')[0] is correct.
                        // But if we delete rows in JS, indexes might be non-sequential.
                        // $request->file('gallery_images') returns array where KEY is the index.
                        
                        $fname = time().'_img_'.Str::random(8).'.'.$f->getClientOriginalExtension();
                        Storage::disk('nextjs')->putFileAs('', $f, $fname);
                        $srcPath = '/uploads/'.$fname;
                    }

                    // Prepare data
                    $dataItem = [
                        'gallery_metadata_id' => $id,
                        'title'         => $title ?? '',
                        'category'      => $request->img_category[$idx] ?? '',
                        'display_order' => $request->img_display_order[$idx] ?? 0,
                    ];
                    
                    // If new image uploaded, update src. If not, keep old src (for update) or empty (for new? but usually required).
                    if ($srcPath) {
                         // If updating, delete old image? 
                         if ($itemId) {
                             $oldItem = DB::table('gallery_images')->where('id', $itemId)->first();
                             if ($oldItem && $oldItem->src) {
                                 $oldF = basename($oldItem->src);
                                 if (Storage::disk('nextjs')->exists($oldF)) Storage::disk('nextjs')->delete($oldF);
                             }
                         }
                         $dataItem['src'] = $srcPath;
                    }

                    if ($itemId && in_array($itemId, $existingIds)) {
                        DB::table('gallery_images')->where('id', $itemId)->update($dataItem);
                    } else {
                        // For insert, src is required usually? Or keep empty.
                        if (!isset($dataItem['src'])) $dataItem['src'] = '';
                        DB::table('gallery_images')->insert($dataItem);
                    }
                }
            }

            return redirect()->route('gallery.index')->with('success', 'Gallery updated successfully');

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed update gallery: ' . $e->getMessage());
        }
    }

    /**
     * DESTROY
     */
    public function destroy($id)
    {
        $metadata = DB::table('gallery_metadata')->where('id', $id)->first();

        // 1. Delete background meta
        if ($metadata->background_image) {
            $f = basename($metadata->background_image);
            if (Storage::disk('nextjs')->exists($f)) {
                Storage::disk('nextjs')->delete($f);
            }
        }

        // 2. Delete gallery images
        $images = DB::table('gallery_images')->where('gallery_metadata_id', $id)->get();
        foreach ($images as $img) {
            if ($img->src) {
                $f = basename($img->src);
                if (Storage::disk('nextjs')->exists($f)) {
                    Storage::disk('nextjs')->delete($f);
                }
            }
        }
        
        DB::table('gallery_images')->where('gallery_metadata_id', $id)->delete();
        DB::table('gallery_metadata')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Gallery deleted!'
        ]);
    }
}
