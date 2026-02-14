<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

use App\Services\ImageKitService;

class GalleryController extends Controller
{
    protected $imageKit;

    public function __construct(ImageKitService $imageKit)
    {
        $this->imageKit = $imageKit;
    }

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
                    return '<img src="' . $row->background_image . '" style="height:50px; border-radius:6px;">';
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
            $bgFileId = null;
            if ($request->hasFile('background_image')) {
                $upload = $this->imageKit->upload($request->file('background_image'), 'gallery');
                if ($upload) {
                    $bgPath = $upload->url;
                    $bgFileId = $upload->fileId;
                }
            }

            // 2. Insert Metadata
            $metaId = DB::table('gallery_metadata')->insertGetId([
                'title'            => $request->title,
                'subtitle'         => $request->subtitle,
                'description'      => $request->description,
                'background_image' => $bgPath,
                'background_image_file_id' => $bgFileId,
            ]);

            // 3. Insert Gallery Images
            if ($request->img_title) {
                foreach ($request->img_title as $idx => $title) {
                    // Upload image for this item if exists
                    $srcPath = null;
                    $srcFileId = null;
                    if ($request->hasFile("gallery_images.$idx")) {
                        $upload = $this->imageKit->upload($request->file("gallery_images")[$idx], 'gallery/images');
                        if ($upload) {
                            $srcPath = $upload->url;
                            $srcFileId = $upload->fileId;
                        }
                    }

                    // Only insert if title OR image is provided (or just image? prompt says src required logic usually)
                    // But standard logic: skip empty rows. 
                    if (!$srcPath && !$title) continue;

                    DB::table('gallery_images')->insert([
                        'gallery_metadata_id' => $metaId,
                        'src'           => $srcPath ?? '',
                        'image_path_file_id' => $srcFileId,
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
                if ($metadata->background_image_file_id) {
                    $this->imageKit->delete($metadata->background_image_file_id);
                }
                // Upload new bg
                $upload = $this->imageKit->upload($request->file('background_image'), 'gallery');
                if ($upload) {
                    $updateData['background_image'] = $upload->url;
                    $updateData['background_image_file_id'] = $upload->fileId;
                }
            }

            DB::table('gallery_metadata')->where('id', $id)->update($updateData);

            // 2. Sync Gallery Images
            $existingIds = DB::table('gallery_images')->where('gallery_metadata_id', $id)->pluck('id')->toArray();
            $submittedIds = array_filter($request->img_id ?? []);

            // Delete removed items
            $toDelete = array_diff($existingIds, $submittedIds);
            foreach ($toDelete as $delId) {
                $imgRow = DB::table('gallery_images')->where('id', $delId)->first();
                if ($imgRow && $imgRow->image_path_file_id) {
                    $this->imageKit->delete($imgRow->image_path_file_id);
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
                    $srcFileId = null;
                    if ($request->hasFile("gallery_images.$idx")) {
                        $upload = $this->imageKit->upload($request->file("gallery_images")[$idx], 'gallery/images');
                        if ($upload) {
                            $srcPath = $upload->url;
                            $srcFileId = $upload->fileId;
                        }
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
                             if ($oldItem && $oldItem->image_path_file_id) {
                                 $this->imageKit->delete($oldItem->image_path_file_id);
                             }
                         }
                         $dataItem['src'] = $srcPath;
                         $dataItem['image_path_file_id'] = $srcFileId;
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
        if ($metadata->background_image_file_id) {
            $this->imageKit->delete($metadata->background_image_file_id);
        }

        // 2. Delete gallery images
        $images = DB::table('gallery_images')->where('gallery_metadata_id', $id)->get();
        foreach ($images as $img) {
            if ($img->image_path_file_id) {
                $this->imageKit->delete($img->image_path_file_id);
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
