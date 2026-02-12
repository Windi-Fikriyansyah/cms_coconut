<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class FooterController extends Controller
{
    /**
     * INDEX
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('footer_settings')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('description', function($row){
                    return \Illuminate\Support\Str::limit($row->description, 50);
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="btn-group">
                            <a href="'.route('footer.edit', $row->id).'" class="btn btn-sm btn-info">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <button class="btn btn-sm btn-danger delete-btn" data-url="'.route('footer.destroy', $row->id).'">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $count = DB::table('footer_settings')->count();
        return view('footer.index', compact('count'));
    }

    /**
     * CREATE
     */
    public function create()
    {
        return view('footer.create');
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'description'   => 'nullable|string',
            'linkedin_url'  => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'facebook_url'  => 'nullable|url',
            'youtube_url'   => 'nullable|url',
            'tiktok_url'    => 'nullable|url',
        ]);

        try {
            DB::table('footer_settings')->insert([
                'description'   => $request->description,
                'linkedin_url'  => $request->linkedin_url,
                'instagram_url' => $request->instagram_url,
                'facebook_url'  => $request->facebook_url,
                'youtube_url'   => $request->youtube_url,
                'tiktok_url'    => $request->tiktok_url,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            return redirect()->route('footer.index')->with('success', 'Footer settings created successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed create footer settings: ' . $e->getMessage());
        }
    }

    /**
     * EDIT
     */
    public function edit($id)
    {
        $footer = DB::table('footer_settings')->where('id', $id)->first();
        if (!$footer) {
            return redirect()->route('footer.index')->with('error', 'Footer settings not found');
        }
        return view('footer.edit', compact('footer'));
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'description'   => 'nullable|string',
            'linkedin_url'  => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'facebook_url'  => 'nullable|url',
            'youtube_url'   => 'nullable|url',
            'tiktok_url'    => 'nullable|url',
        ]);

        try {
            DB::table('footer_settings')->where('id', $id)->update([
                'description'   => $request->description,
                'linkedin_url'  => $request->linkedin_url,
                'instagram_url' => $request->instagram_url,
                'facebook_url'  => $request->facebook_url,
                'youtube_url'   => $request->youtube_url,
                'tiktok_url'    => $request->tiktok_url,
                'updated_at'    => now(),
            ]);

            return redirect()->route('footer.index')->with('success', 'Footer settings updated successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed update footer settings: ' . $e->getMessage());
        }
    }

    /**
     * DESTROY
     */
    public function destroy($id)
    {
        DB::table('footer_settings')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Footer settings deleted!'
        ]);
    }
}
