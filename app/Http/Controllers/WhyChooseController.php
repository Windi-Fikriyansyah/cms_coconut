<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

use App\Services\ImageKitService;

class WhyChooseController extends Controller
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

            $data = DB::table('why_choose_us_metadata')->get();

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('images', function ($row) {
                    if (!$row->image) return 'No Image';

                    $images = json_decode($row->image, true);
                    if (!is_array($images)) return '-';

                    $html = '';
                    // Display up to 3 images as preview
                    foreach (array_slice($images, 0, 3) as $img) {
                        $url = is_array($img) ? $img['url'] : $img;
                        $html .= '<img src="'.$url.'" style="height:40px;margin-right:4px;border-radius:4px;">';
                    }
                    if (count($images) > 3) {
                        $html .= '<span class="badge bg-secondary">+ '.(count($images)-3).'</span>';
                    }

                    return $html;
                })

                ->addColumn('items_count', function ($row) {
                    return DB::table('why_choose_us_section')
                        ->where('id_why_meta', $row->id)
                        ->count();
                })

                ->addColumn('action', function ($row) {
                    return '
                        <div class="btn-group">
                            <a href="'.route('why_choose.edit', $row->id).'" class="btn btn-sm btn-info">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <button class="btn btn-sm btn-danger delete-btn"
                                data-url="'.route('why_choose.destroy', $row->id).'">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    ';
                })

                ->rawColumns(['images', 'action'])
                ->make(true);
        }
    $count = DB::table('why_choose_us_metadata')->count();
        return view('why_choose.index', compact('count'));
    }

    /**
     * CREATE
     */
    public function create()
    {
        return view('why_choose.create');
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
                    $upload = $this->imageKit->upload($file, 'why_choose');
                    if ($upload) {
                        $sectionImages[] = [
                            'url' => $upload->url,
                            'fileId' => $upload->fileId
                        ];
                    }
                }
            }

            // 2. Insert Section
            $sectionId = DB::table('why_choose_us_metadata')->insertGetId([
                'image' => json_encode($sectionImages),
            ]);

            // 3. Insert Items
            if ($request->item_title) {
                foreach ($request->item_title as $idx => $title) {
                    if (!$title) continue;

                    DB::table('why_choose_us_section')->insert([
                        'id_why_meta' => $sectionId,
                        'icon'          => $request->item_icon[$idx] ?? '',
                        'title'         => $title,
                        'description'   => $request->item_description[$idx] ?? '',
                        'display_order' => $request->item_display_order[$idx] ?? 0,
                    ]);
                }
            }

            return redirect()->route('why_choose.index')->with('success', 'Why Choose Section created successfully');

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed create Why Choose section: ' . $e->getMessage());
        }
    }

    /**
     * EDIT
     */
    public function edit($id)
    {
        $section = DB::table('why_choose_us_metadata')->where('id', $id)->first();
        $items = DB::table('why_choose_us_section')
            ->where('id_why_meta', $id)
            ->orderBy('display_order')
            ->get();

        return view('why_choose.edit', compact('section', 'items'));
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
            // 1. Handle Images
            $finalImages = [];
            $existing_raw = $request->input('existing_images', []);
            $oldSection = DB::table('why_choose_us_metadata')->where('id', $id)->first();
            $oldImages = json_decode($oldSection->image ?? '[]', true) ?: [];

            // Filter existing images
            foreach ($existing_raw as $url) {
                foreach ($oldImages as $oi) {
                    if ((is_array($oi) && $oi['url'] === $url) || (!is_array($oi) && $oi === $url)) {
                        $finalImages[] = $oi;
                        break;
                    }
                }
            }

            if ($request->hasFile('section_images')) {
                foreach ($request->file('section_images') as $file) {
                    $upload = $this->imageKit->upload($file, 'why_choose');
                    if ($upload) {
                        $finalImages[] = [
                            'url' => $upload->url,
                            'fileId' => $upload->fileId
                        ];
                    }
                }
            }

            // Clean up deleted images
            foreach ($oldImages as $oldImg) {
                $remains = false;
                foreach ($finalImages as $fi) {
                    $oldUrl = is_array($oldImg) ? $oldImg['url'] : $oldImg;
                    $newUrl = is_array($fi) ? $fi['url'] : $fi;
                    if ($oldUrl === $newUrl) {
                        $remains = true;
                        break;
                    }
                }
                if (!$remains && is_array($oldImg) && isset($oldImg['fileId'])) {
                    $this->imageKit->delete($oldImg['fileId']);
                }
            }

            DB::table('why_choose_us_metadata')->where('id', $id)->update([
                'image' => json_encode($finalImages)
            ]);

            // 2. Sync Items
            $existingItemIds = DB::table('why_choose_us_section')
                ->where('id_why_meta', $id)
                ->pluck('id')
                ->toArray();

            $submittedItemIds = array_filter($request->item_id ?? []);

            // Delete removed items
            $toDelete = array_diff($existingItemIds, $submittedItemIds);
            if (!empty($toDelete)) {
                DB::table('why_choose_us_section')->whereIn('id', $toDelete)->delete();
            }

            // Update or Insert Items
            if ($request->item_title) {
                foreach ($request->item_title as $idx => $title) {
                    if (!$title) continue;

                    $itemId = $request->item_id[$idx] ?? null;

                    $itemData = [
                        'id_why_meta' => $id,
                        'icon'          => $request->item_icon[$idx] ?? '',
                        'title'         => $title,
                        'description'   => $request->item_description[$idx] ?? '',
                        'display_order' => $request->item_display_order[$idx] ?? 0,
                    ];

                    if ($itemId && in_array($itemId, $existingItemIds)) {
                        DB::table('why_choose_us_section')->where('id', $itemId)->update($itemData);
                    } else {
                        DB::table('why_choose_us_section')->insert($itemData);
                    }
                }
            }

            return redirect()->route('why_choose.index')->with('success', 'Why Choose Section updated successfully');

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed update Why Choose section: ' . $e->getMessage());
        }
    }

    /**
     * DESTROY
     */
    public function destroy($id)
    {
        $section = DB::table('why_choose_us_metadata')->where('id', $id)->first();

        // Delete section images
        if ($section->image) {
            $images = json_decode($section->image, true);
            if (is_array($images)) {
                foreach ($images as $img) {
                    if (is_array($img) && isset($img['fileId'])) {
                        $this->imageKit->delete($img['fileId']);
                    }
                }
            }
        }

        // Items don't have images in this requirement, so just delete rows.
        DB::table('why_choose_us_section')->where('id_why_meta', $id)->delete();
        DB::table('why_choose_us_metadata')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Why Choose Section deleted!'
        ]);
    }
}
