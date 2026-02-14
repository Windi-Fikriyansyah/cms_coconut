<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Services\ImageKitService;

class TestimoniController extends Controller
{
    protected $imageKit;

    public function __construct(ImageKitService $imageKit)
    {
        $this->imageKit = $imageKit;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('testimonials')->orderBy('display_order', 'asc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    if (!$row->image) return 'No Image';
                    return '<img src="'.$row->image.'" style="height:50px;border-radius:6px;">';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="btn-group">
                            <a href="'.route('testimoni.edit', $row->id).'" class="btn btn-sm btn-info">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <button class="btn btn-sm btn-danger delete-btn" data-url="'.route('testimoni.destroy', $row->id).'">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['image', 'action'])
                ->make(true);
        }

        $metadata = DB::table('testimonials_metadata')->first();
        return view('testimoni.index', compact('metadata'));
    }

    public function create()
    {
        return view('testimoni.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'role' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:webp,jpg,jpeg,png|max:2048',
            'rating' => 'required|integer|min:1|max:5',
            'display_order' => 'required|integer',
        ]);

        try {
            $data = $request->only(['name', 'role', 'content', 'rating', 'display_order']);
            $data['created_at'] = now();
            $data['updated_at'] = now();

            if ($request->hasFile('image')) {
                $upload = $this->imageKit->upload($request->file('image'), 'testimonials');
                if ($upload) {
                    $data['image'] = $upload->url;
                    // Note: If image_file_id column doesn't exist, this might fail unless we check or skip it.
                    // Given the constraint "tanpa model, tanpa migrate", I'll check if column exists before adding.
                    if (\Illuminate\Support\Facades\Schema::hasColumn('testimonials', 'image_file_id')) {
                        $data['image_file_id'] = $upload->fileId;
                    }
                }
            }

            DB::table('testimonials')->insert($data);

            return redirect()->route('testimoni.index')->with('success', 'Testimonial created successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to create testimonial: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $testimoni = DB::table('testimonials')->where('id', $id)->first();
        if (!$testimoni) abort(404);

        return view('testimoni.edit', compact('testimoni'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'role' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:webp,jpg,jpeg,png|max:2048',
            'rating' => 'required|integer|min:1|max:5',
            'display_order' => 'required|integer',
        ]);

        try {
            $testimoni = DB::table('testimonials')->where('id', $id)->first();
            $data = $request->only(['name', 'role', 'content', 'rating', 'display_order']);
            $data['updated_at'] = now();

            if ($request->hasFile('image')) {
                // Delete old image if tracking fileId
                if (isset($testimoni->image_file_id) && $testimoni->image_file_id) {
                    $this->imageKit->delete($testimoni->image_file_id);
                }

                $upload = $this->imageKit->upload($request->file('image'), 'testimonials');
                if ($upload) {
                    $data['image'] = $upload->url;
                    if (\Illuminate\Support\Facades\Schema::hasColumn('testimonials', 'image_file_id')) {
                        $data['image_file_id'] = $upload->fileId;
                    }
                }
            }

            DB::table('testimonials')->where('id', $id)->update($data);

            return redirect()->route('testimoni.index')->with('success', 'Testimonial updated successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to update testimonial: ' . $e->getMessage());
        }
    }

    public function updateMetadata(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'subtitle' => 'required|string',
        ]);

        try {
            $exists = DB::table('testimonials_metadata')->first();
            if ($exists) {
                DB::table('testimonials_metadata')->where('id', $exists->id)->update([
                    'title' => $request->title,
                    'subtitle' => $request->subtitle,
                ]);
            } else {
                DB::table('testimonials_metadata')->insert([
                    'title' => $request->title,
                    'subtitle' => $request->subtitle,
                ]);
            }

            return back()->with('success', 'Metadata updated successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to update metadata: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $testimoni = DB::table('testimonials')->where('id', $id)->first();
            if ($testimoni) {
                if (isset($testimoni->image_file_id) && $testimoni->image_file_id) {
                    $this->imageKit->delete($testimoni->image_file_id);
                }
                DB::table('testimonials')->where('id', $id)->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
