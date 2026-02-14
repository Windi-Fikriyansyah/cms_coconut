<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Services\ImageKitService;

class TeamController extends Controller
{
    protected $imageKit;

    public function __construct(ImageKitService $imageKit)
    {
        $this->imageKit = $imageKit;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('team_members')->orderBy('display_order', 'asc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    if (!$row->image) return 'No Image';
                    return '<img src="'.$row->image.'" style="height:50px;border-radius:6px;">';
                })
                ->addColumn('socials', function($row) {
                    $html = '';
                    if ($row->linkedin_url) {
                        $html .= '<a href="'.$row->linkedin_url.'" target="_blank" class="btn btn-sm btn-primary me-1"><i class="bi bi-linkedin"></i></a>';
                    }
                    if ($row->instagram_url) {
                        $html .= '<a href="'.$row->instagram_url.'" target="_blank" class="btn btn-sm btn-danger"><i class="bi bi-instagram"></i></a>';
                    }
                    return $html ?: '-';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="btn-group">
                            <a href="'.route('team.edit', $row->id).'" class="btn btn-sm btn-info">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <button class="btn btn-sm btn-danger delete-btn" data-url="'.route('team.destroy', $row->id).'">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['image', 'socials', 'action'])
                ->make(true);
        }

        $metadata = DB::table('team_metadata')->first();
        return view('team.index', compact('metadata'));
    }

    public function create()
    {
        return view('team.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'role' => 'required|string',
            'image' => 'nullable|image|mimes:webp,jpg,jpeg,png|max:2048',
            'linkedin_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'display_order' => 'required|integer',
        ]);

        try {
            $data = $request->only(['name', 'role', 'linkedin_url', 'instagram_url', 'display_order']);
            $data['created_at'] = now();
            $data['updated_at'] = now();

            if ($request->hasFile('image')) {
                $upload = $this->imageKit->upload($request->file('image'), 'team');
                if ($upload) {
                    $data['image'] = $upload->url;
                    if (\Illuminate\Support\Facades\Schema::hasColumn('team_members', 'image_file_id')) {
                        $data['image_file_id'] = $upload->fileId;
                    }
                }
            }

            DB::table('team_members')->insert($data);

            return redirect()->route('team.index')->with('success', 'Team member added successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to add team member: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $member = DB::table('team_members')->where('id', $id)->first();
        if (!$member) abort(404);

        return view('team.edit', compact('member'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'role' => 'required|string',
            'image' => 'nullable|image|mimes:webp,jpg,jpeg,png|max:2048',
            'linkedin_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'display_order' => 'required|integer',
        ]);

        try {
            $member = DB::table('team_members')->where('id', $id)->first();
            $data = $request->only(['name', 'role', 'linkedin_url', 'instagram_url', 'display_order']);
            $data['updated_at'] = now();

            if ($request->hasFile('image')) {
                if (isset($member->image_file_id) && $member->image_file_id) {
                    $this->imageKit->delete($member->image_file_id);
                }

                $upload = $this->imageKit->upload($request->file('image'), 'team');
                if ($upload) {
                    $data['image'] = $upload->url;
                    if (\Illuminate\Support\Facades\Schema::hasColumn('team_members', 'image_file_id')) {
                        $data['image_file_id'] = $upload->fileId;
                    }
                }
            }

            DB::table('team_members')->where('id', $id)->update($data);

            return redirect()->route('team.index')->with('success', 'Team member updated successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to update team member: ' . $e->getMessage());
        }
    }

    public function updateMetadata(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'subtitle' => 'required|string',
        ]);

        try {
            $exists = DB::table('team_metadata')->first();
            if ($exists) {
                DB::table('team_metadata')->where('id', $exists->id)->update([
                    'title' => $request->title,
                    'subtitle' => $request->subtitle,
                ]);
            } else {
                DB::table('team_metadata')->insert([
                    'title' => $request->title,
                    'subtitle' => $request->subtitle,
                ]);
            }

            return back()->with('success', 'Header updated successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to update header: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $member = DB::table('team_members')->where('id', $id)->first();
            if ($member) {
                if (isset($member->image_file_id) && $member->image_file_id) {
                    $this->imageKit->delete($member->image_file_id);
                }
                DB::table('team_members')->where('id', $id)->delete();
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
