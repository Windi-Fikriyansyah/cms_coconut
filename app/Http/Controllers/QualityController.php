<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class QualityController extends Controller
{
    /**
     * INDEX
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = DB::table('quality_commitment_section')->get();

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('images', function ($row) {
                    if (!$row->image) return 'No Image';

                    $images = json_decode($row->image, true);
                    if (!is_array($images)) return '-';

                    $html = '';
                    // Display up to 3 images as preview
                    foreach (array_slice($images, 0, 3) as $img) {
                        $url = Storage::disk('nextjs')->url(
                            str_replace('/uploads/', '', $img)
                        );
                        $html .= '<img src="'.$url.'" style="height:40px;margin-right:4px;border-radius:4px;">';
                    }
                    if (count($images) > 3) {
                        $html .= '<span class="badge bg-secondary">+ '.(count($images)-3).'</span>';
                    }

                    return $html;
                })

                ->addColumn('items_count', function ($row) {
                    return DB::table('quality_commitment_items')
                        ->where('quality_commitment_section_id', $row->id)
                        ->count();
                })

                ->addColumn('action', function ($row) {
                    return '
                        <div class="btn-group">
                            <a href="'.route('quality.edit', $row->id).'" class="btn btn-sm btn-info">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <button class="btn btn-sm btn-danger delete-btn"
                                data-url="'.route('quality.destroy', $row->id).'">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    ';
                })

                ->rawColumns(['images', 'action'])
                ->make(true);
        }
$count = DB::table('quality_commitment_section')->count();
        return view('quality.index', compact('count'));
    }

    /**
     * CREATE
     */
    public function create()
    {
        return view('quality.create');
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            // Section Images
            'section_images'     => 'nullable|array',
            'section_images.*'   => 'nullable|image|mimes:webp|max:2048',

            // Items
            'item_icon'            => 'nullable|array',
            'item_icon.*'          => 'nullable|string',
            'item_title'           => 'nullable|array',
            'item_title.*'         => 'nullable|string',
            'item_description'     => 'nullable|array',
            'item_description.*'   => 'nullable|string',
            'item_display_order'   => 'nullable|array',
            'item_display_order.*' => 'nullable|integer',
        ]);

        try {
            // 1. Handle Section Images
            $sectionImages = [];
            if ($request->hasFile('section_images')) {
                foreach ($request->file('section_images') as $file) {
                    $name = time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                    Storage::disk('nextjs')->putFileAs('', $file, $name);
                    $sectionImages[] = '/uploads/'.$name;
                }
            }

            // 2. Insert Section
            $sectionId = DB::table('quality_commitment_section')->insertGetId([
                'image' => json_encode($sectionImages),
            ]);

            // 3. Insert Items
            if ($request->item_title) {
                foreach ($request->item_title as $idx => $title) {
                    if (!$title) continue;

                    DB::table('quality_commitment_items')->insert([
                        'quality_commitment_section_id' => $sectionId,
                        'icon'          => $request->item_icon[$idx] ?? '',
                        'title'         => $title,
                        'description'   => $request->item_description[$idx] ?? '',
                        'display_order' => $request->item_display_order[$idx] ?? 0,
                    ]);
                }
            }

            return redirect()->route('quality.index')->with('success', 'Quality Section created successfully');

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed create quality section: ' . $e->getMessage());
        }
    }

    /**
     * EDIT
     */
    public function edit($id)
    {
        $section = DB::table('quality_commitment_section')->where('id', $id)->first();
        $items = DB::table('quality_commitment_items')
            ->where('quality_commitment_section_id', $id)
            ->orderBy('display_order')
            ->get();

        return view('quality.edit', compact('section', 'items'));
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            // existing_images is array of strings (paths)
            'existing_images'    => 'nullable|array',
            'existing_images.*'  => 'nullable|string',
            
            // New uploads
            'section_images'     => 'nullable|array',
            'section_images.*'   => 'nullable|image|mimes:webp|max:2048',

            // Items
            'item_id'              => 'nullable|array',
            'item_icon'            => 'nullable|array',
            'item_icon.*'          => 'nullable|string',
            'item_title'           => 'nullable|array',
            'item_title.*'         => 'nullable|string',
            'item_description'     => 'nullable|array',
            'item_description.*'   => 'nullable|string',
            'item_display_order'   => 'nullable|array',
            'item_display_order.*' => 'nullable|integer',
        ]);

        try {
            // 1. Handle Images: Merge kept existing images + newly uploaded ones
            $finalImages = $request->existing_images ?? [];

            if ($request->hasFile('section_images')) {
                foreach ($request->file('section_images') as $file) {
                    $name = time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                    Storage::disk('nextjs')->putFileAs('', $file, $name);
                    $finalImages[] = '/uploads/'.$name;
                }
            }

            // Clean up deleted images (those in DB but not in finalImages)
            $oldSection = DB::table('quality_commitment_section')->where('id', $id)->first();
            $oldImages = json_decode($oldSection->image ?? '[]', true) ?: [];
            
            // Find images that were in DB but are NOT in the submitted 'existing_images' list
            // (User deleted them in UI)
            // Note: Use array_values to re-index
            $finalImages = array_values($finalImages);
            
            foreach ($oldImages as $oldImg) {
                if (!in_array($oldImg, $finalImages)) {
                    // Delete from storage
                    $file = basename($oldImg);
                    if (Storage::disk('nextjs')->exists($file)) {
                        Storage::disk('nextjs')->delete($file);
                    }
                }
            }

            DB::table('quality_commitment_section')->where('id', $id)->update([
                'image' => json_encode($finalImages)
            ]);

            // 2. Sync Items
            $existingItemIds = DB::table('quality_commitment_items')
                ->where('quality_commitment_section_id', $id)
                ->pluck('id')
                ->toArray();

            $submittedItemIds = array_filter($request->item_id ?? []);

            // Delete removed items
            $toDelete = array_diff($existingItemIds, $submittedItemIds);
            if (!empty($toDelete)) {
                DB::table('quality_commitment_items')->whereIn('id', $toDelete)->delete();
            }

            // Update or Insert Items
            if ($request->item_title) {
                foreach ($request->item_title as $idx => $title) {
                    if (!$title) continue;

                    $itemId = $request->item_id[$idx] ?? null;

                    $itemData = [
                        'quality_commitment_section_id' => $id,
                        'icon'          => $request->item_icon[$idx] ?? '',
                        'title'         => $title,
                        'description'   => $request->item_description[$idx] ?? '',
                        'display_order' => $request->item_display_order[$idx] ?? 0,
                    ];

                    if ($itemId && in_array($itemId, $existingItemIds)) {
                        DB::table('quality_commitment_items')->where('id', $itemId)->update($itemData);
                    } else {
                        DB::table('quality_commitment_items')->insert($itemData);
                    }
                }
            }

            return redirect()->route('quality.index')->with('success', 'Quality Section updated successfully');

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed update quality section: ' . $e->getMessage());
        }
    }

    /**
     * DESTROY
     */
    public function destroy($id)
    {
        $section = DB::table('quality_commitment_section')->where('id', $id)->first();

        // Delete section images
        if ($section->image) {
            $images = json_decode($section->image, true);
            if (is_array($images)) {
                foreach ($images as $img) {
                    $file = basename($img);
                    if (Storage::disk('nextjs')->exists($file)) {
                        Storage::disk('nextjs')->delete($file);
                    }
                }
            }
        }

        // Items don't have images in this requirement, so just delete rows.
        DB::table('quality_commitment_items')->where('quality_commitment_section_id', $id)->delete();
        DB::table('quality_commitment_section')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Quality Section deleted!'
        ]);
    }
}
