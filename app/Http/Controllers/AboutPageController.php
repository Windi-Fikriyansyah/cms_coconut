<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class AboutPageController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('about_page')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('hero_image', function ($row) {
                    if (!$row->hero_image) return 'No Image';
                    $url = Storage::disk('nextjs')->url(str_replace('/uploads/', '', $row->hero_image));
                    return '<img src="'.$url.'" style="height:50px;border-radius:6px;">';
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
                $file = $request->file('hero_image');
                $name = time().'_hero_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                Storage::disk('nextjs')->putFileAs('', $file, $name);
                $data['hero_image'] = '/uploads/'.$name;
            }

            // Handle journey_image (array)
            $journey_images = [];
            if ($request->hasFile('journey_image')) {
                foreach ($request->file('journey_image') as $file) {
                    $name = time().'_journey_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                    Storage::disk('nextjs')->putFileAs('', $file, $name);
                    $journey_images[] = '/uploads/'.$name;
                }
            }
            $data['journey_image'] = json_encode($journey_images);

            // Handle commitment_image
            if ($request->hasFile('commitment_image')) {
                $file = $request->file('commitment_image');
                $name = time().'_commitment_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                Storage::disk('nextjs')->putFileAs('', $file, $name);
                $data['commitment_image'] = '/uploads/'.$name;
            }

            // JSON data
            $data['mission_points'] = json_encode($request->mission_points);
            $data['values_data'] = json_encode($request->values_data);
            
            // Process items handling image uploads if any
            $process_items = $request->process_items;
            if ($request->hasFile('process_image')) {
                foreach ($request->file('process_image') as $index => $file) {
                    $name = time().'_process_'.$index.'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                    Storage::disk('nextjs')->putFileAs('', $file, $name);
                    if (isset($process_items[$index])) {
                        $process_items[$index]['image'] = '/uploads/'.$name;
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
                if ($about->hero_image) {
                    Storage::disk('nextjs')->delete(basename($about->hero_image));
                }
                $file = $request->file('hero_image');
                $name = time().'_hero_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                Storage::disk('nextjs')->putFileAs('', $file, $name);
                $data['hero_image'] = '/uploads/'.$name;
            }

            // Handle journey_image
            $final_journey_images = [];
            $existing_images = $request->input('existing_journey_images', []);
            $replace_files = $request->file('journey_image_replace', []);
            $new_files = $request->file('journey_image_new', []);

            // Handle replacements for existing images
            foreach ($existing_images as $idx => $img_path) {
                if (isset($replace_files[$idx])) {
                    // Delete old one if replaced
                    Storage::disk('nextjs')->delete(basename($img_path));
                    
                    $file = $replace_files[$idx];
                    $name = time().'_journey_replace_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                    Storage::disk('nextjs')->putFileAs('', $file, $name);
                    $final_journey_images[] = '/uploads/'.$name;
                } else {
                    $final_journey_images[] = $img_path;
                }
            }

            // Handle new images
            if ($new_files) {
                foreach ($new_files as $file) {
                    if (count($final_journey_images) < 3) {
                        $name = time().'_journey_new_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                        Storage::disk('nextjs')->putFileAs('', $file, $name);
                        $final_journey_images[] = '/uploads/'.$name;
                    }
                }
            }
            
            // Clean up deleted images (if user removed a journey-image-item entirely)
            $old_journey_images = json_decode($about->journey_image ?? '[]') ?: [];
            foreach ($old_journey_images as $old_img) {
                if (!in_array($old_img, $final_journey_images)) {
                    // Check if this image was part of replacements (already deleted) 
                    // or if it was just removed from the list
                    if (!isset($replace_files[array_search($old_img, $old_journey_images)])) {
                         Storage::disk('nextjs')->delete(basename($old_img));
                    }
                }
            }

            $data['journey_image'] = json_encode(array_slice($final_journey_images, 0, 3));

            // Handle commitment_image
            if ($request->hasFile('commitment_image')) {
                if ($about->commitment_image) {
                    Storage::disk('nextjs')->delete(basename($about->commitment_image));
                }
                $file = $request->file('commitment_image');
                $name = time().'_commitment_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                Storage::disk('nextjs')->putFileAs('', $file, $name);
                $data['commitment_image'] = '/uploads/'.$name;
            }

            $data['mission_points'] = json_encode($request->mission_points);
            $data['values_data'] = json_encode($request->values_data);

            $process_items = $request->process_items;
            if ($request->hasFile('process_image')) {
                foreach ($request->file('process_image') as $index => $file) {
                    // Delete old image if exists
                    if (isset($process_items[$index]['image']) && !empty($process_items[$index]['image'])) {
                         Storage::disk('nextjs')->delete(basename($process_items[$index]['image']));
                    }
                    $name = time().'_process_'.$index.'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                    Storage::disk('nextjs')->putFileAs('', $file, $name);
                    $process_items[$index]['image'] = '/uploads/'.$name;
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
            if ($about->hero_image) Storage::disk('nextjs')->delete(basename($about->hero_image));
            if ($about->commitment_image) Storage::disk('nextjs')->delete(basename($about->commitment_image));
            
            $journey_images = json_decode($about->journey_image ?? '[]') ?: [];
            foreach ($journey_images as $img) {
                Storage::disk('nextjs')->delete(basename($img));
            }

            $process_items = json_decode($about->process_items ?? '[]') ?: [];
            foreach ($process_items as $item) {
                if (isset($item->image)) Storage::disk('nextjs')->delete(basename($item->image));
            }

            DB::table('about_page')->where('id', $id)->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully'
        ]);
    }
}
