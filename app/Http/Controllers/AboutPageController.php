<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

use App\Services\ImageKitService;

class AboutPageController extends Controller
{
    protected $imageKit;

    public function __construct(ImageKitService $imageKit)
    {
        $this->imageKit = $imageKit;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('about_page')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('hero_image', function ($row) {
                    if (!$row->hero_image) return 'No Image';
                    return '<img src="'.$row->hero_image.'" style="height:50px;border-radius:6px;">';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="btn-group">
                            <a href="'.route('about-page.edit', $row->id).'" class="btn btn-sm btn-info">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <button class="btn btn-sm btn-danger delete-btn"
                                data-url="'.route('about-page.destroy', $row->id).'">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['hero_image', 'action'])
                ->make(true);
        }

        $count = DB::table('about_page')->count();
        return view('about_page.index', compact('count'));
    }

    public function create()
    {
        return view('about_page.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'hero_badge' => 'nullable|string',
            'hero_title' => 'required|string',
            'hero_description' => 'required|string',
            'hero_image' => 'nullable|image|mimes:webp|max:2048',
            'journey_title' => 'required|string',
            'journey_description_1' => 'required|string',
            'journey_description_2' => 'required|string',
            'journey_image' => 'nullable|array',
            'journey_image.*' => 'image|mimes:webp|max:2048',
            'vision_title' => 'required|string',
            'vision_description' => 'required|string',
            'mission_title' => 'required|string',
            'mission_points' => 'required|array',
            'values_data' => 'required|array',
            'commitment_title' => 'required|string',
            'commitment_description' => 'required|string',
            'commitment_image' => 'nullable|image|mimes:webp|max:2048',
            'process_title' => 'required|string',
            'process_subtitle' => 'required|string',
            'process_items' => 'required|array',
        ]);

        try {
            $data = $request->only([
                'hero_badge', 'hero_title', 'hero_description',
                'journey_title', 'journey_description_1', 'journey_description_2',
                'vision_title', 'vision_description', 'mission_title',
                'commitment_title', 'commitment_description',
                'process_title', 'process_subtitle'
            ]);

            // Handle hero_image
            if ($request->hasFile('hero_image')) {
                $upload = $this->imageKit->upload($request->file('hero_image'), 'about');
                if ($upload) {
                    $data['hero_image'] = $upload->url;
                    $data['hero_image_file_id'] = $upload->fileId;
                }
            }

            // Handle journey_image (array)
            $journey_images = [];
            if ($request->hasFile('journey_image')) {
                foreach ($request->file('journey_image') as $file) {
                    $upload = $this->imageKit->upload($file, 'about/journey');
                    if ($upload) {
                        $journey_images[] = [
                            'url' => $upload->url,
                            'fileId' => $upload->fileId
                        ];
                    }
                }
            }
            $data['journey_image'] = json_encode($journey_images);

            // Handle commitment_image
            if ($request->hasFile('commitment_image')) {
                $upload = $this->imageKit->upload($request->file('commitment_image'), 'about/commitment');
                if ($upload) {
                    $data['commitment_image'] = $upload->url;
                    $data['commitment_image_file_id'] = $upload->fileId;
                }
            }

            // JSON data
            $data['mission_points'] = json_encode($request->mission_points);
            $data['values_data'] = json_encode($request->values_data);
            
            // Process items handling image uploads if any
            $process_items = $request->process_items;
            if ($request->hasFile('process_image')) {
                foreach ($request->file('process_image') as $index => $file) {
                    $upload = $this->imageKit->upload($file, 'about/process');
                    if ($upload && isset($process_items[$index])) {
                        $process_items[$index]['image'] = $upload->url;
                        $process_items[$index]['fileId'] = $upload->fileId;
                    }
                }
            }
            $data['process_items'] = json_encode($process_items);

            DB::table('about_page')->insert($data);

            return redirect()->route('about-page.index')->with('success', 'About Page created successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to create About Page: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $about = DB::table('about_page')->where('id', $id)->first();
        if (!$about) abort(404);

        $about->journey_image = json_decode($about->journey_image ?? '[]') ?: [];
        $about->mission_points = json_decode($about->mission_points ?? '[]') ?: [];
        $about->values_data = json_decode($about->values_data ?? '[]') ?: [];
        $about->process_items = json_decode($about->process_items ?? '[]') ?: [];

        return view('about_page.edit', compact('about'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'hero_badge' => 'nullable|string',
            'hero_title' => 'required|string',
            'hero_description' => 'required|string',
            'hero_image' => 'nullable|image|mimes:webp|max:2048',
            'journey_title' => 'required|string',
            'journey_description_1' => 'required|string',
            'journey_description_2' => 'required|string',
            'journey_image' => 'nullable|array',
            'journey_image.*' => 'image|mimes:webp|max:2048',
            'vision_title' => 'required|string',
            'vision_description' => 'required|string',
            'mission_title' => 'required|string',
            'mission_points' => 'required|array',
            'values_data' => 'required|array',
            'commitment_title' => 'required|string',
            'commitment_description' => 'required|string',
            'commitment_image' => 'nullable|image|mimes:webp|max:2048',
            'process_title' => 'required|string',
            'process_subtitle' => 'required|string',
            'process_items' => 'required|array',
        ]);

        try {
            $about = DB::table('about_page')->where('id', $id)->first();
            $data = $request->only([
                'hero_badge', 'hero_title', 'hero_description',
                'journey_title', 'journey_description_1', 'journey_description_2',
                'vision_title', 'vision_description', 'mission_title',
                'commitment_title', 'commitment_description',
                'process_title', 'process_subtitle'
            ]);

            // Handle hero_image
            if ($request->hasFile('hero_image')) {
                if ($about->hero_image_file_id) {
                    $this->imageKit->delete($about->hero_image_file_id);
                }
                $upload = $this->imageKit->upload($request->file('hero_image'), 'about');
                if ($upload) {
                    $data['hero_image'] = $upload->url;
                    $data['hero_image_file_id'] = $upload->fileId;
                }
            }

            // Handle journey_image
            $final_journey_images = [];
            $existing_journey = json_decode($about->journey_image ?? '[]', true) ?: [];

// NORMALIZE (fix jika data lama masih string)
$existing_journey = array_map(function ($item) {
    if (is_string($item)) {
        return [
            'url' => $item,
            'fileId' => null
        ];
    }
    return $item;
}, $existing_journey);

            $existing_urls = $request->input('existing_journey_images', []);
            $replace_files = $request->file('journey_image_replace', []);
            $new_files = $request->file('journey_image_new', []);

            // Handle replacements for existing images
            foreach ($existing_urls as $idx => $img_url) {
    $found_existing = null;

    foreach ($existing_journey as $ej) {

        if (isset($ej['url']) && $ej['url'] === $img_url) {
            $found_existing = $ej;
            break;
        }
    }

                if (isset($replace_files[$idx])) {
                    // Delete old one if replaced
                    if ($found_existing && isset($found_existing['fileId'])) {
                        $this->imageKit->delete($found_existing['fileId']);
                    }
                    
                    $upload = $this->imageKit->upload($replace_files[$idx], 'about/journey');
                    if ($upload) {
                        $final_journey_images[] = [
                            'url' => $upload->url,
                            'fileId' => $upload->fileId
                        ];
                    }
                } else if ($found_existing) {
                    $final_journey_images[] = $found_existing;
                }
            }

            // Handle new images
            if ($new_files) {
                foreach ($new_files as $file) {
                    if (count($final_journey_images) < 3) {
                        $upload = $this->imageKit->upload($file, 'about/journey');
                        if ($upload) {
                            $final_journey_images[] = [
                                'url' => $upload->url,
                                'fileId' => $upload->fileId
                            ];
                        }
                    }
                }
            }
            
            // Clean up deleted images
            foreach ($existing_journey as $ej) {
                $remains = false;
                foreach($final_journey_images as $fj) {
                    if($fj['url'] === $ej['url']) {
                        $remains = true;
                        break;
                    }
                }
                if (!$remains && isset($ej['fileId'])) {
                    $this->imageKit->delete($ej['fileId']);
                }
            }

            $data['journey_image'] = json_encode(array_slice($final_journey_images, 0, 3));

            // Handle commitment_image
            if ($request->hasFile('commitment_image')) {
                if ($about->commitment_image_file_id) {
                    $this->imageKit->delete($about->commitment_image_file_id);
                }
                $upload = $this->imageKit->upload($request->file('commitment_image'), 'about/commitment');
                if ($upload) {
                    $data['commitment_image'] = $upload->url;
                    $data['commitment_image_file_id'] = $upload->fileId;
                }
            }

            $data['mission_points'] = json_encode($request->mission_points);
            $data['values_data'] = json_encode($request->values_data);

            $process_items = $request->process_items;
            if ($request->hasFile('process_image')) {
                foreach ($request->file('process_image') as $index => $file) {
                    // Delete old image if exists
                    if (isset($process_items[$index]['fileId'])) {
                        $this->imageKit->delete($process_items[$index]['fileId']);
                    }
                    $upload = $this->imageKit->upload($file, 'about/process');
                    if ($upload) {
                        $process_items[$index]['image'] = $upload->url;
                        $process_items[$index]['fileId'] = $upload->fileId;
                    }
                }
            }
            $data['process_items'] = json_encode($process_items);

            DB::table('about_page')->where('id', $id)->update($data);

            return redirect()->route('about-page.index')->with('success', 'About Page updated successfully');
        } catch (\Exception $e) {
            
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to update About Page: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $about = DB::table('about_page')->where('id', $id)->first();
        if ($about) {
            if ($about->hero_image_file_id) $this->imageKit->delete($about->hero_image_file_id);
            if ($about->commitment_image_file_id) $this->imageKit->delete($about->commitment_image_file_id);
            
            $journey_images = json_decode($about->journey_image ?? '[]', true) ?: [];
            foreach ($journey_images as $img) {
                if (isset($img['fileId'])) $this->imageKit->delete($img['fileId']);
            }

            $process_items = json_decode($about->process_items ?? '[]', true) ?: [];
            foreach ($process_items as $item) {
                if (isset($item['fileId'])) $this->imageKit->delete($item['fileId']);
            }

            DB::table('about_page')->where('id', $id)->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully'
        ]);
    }
}
