<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

use App\Services\ImageKitService;

class CertificateController extends Controller
{
    protected $imageKit;

    public function __construct(ImageKitService $imageKit)
    {
        $this->imageKit = $imageKit;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('certificates')->orderBy('display_order')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    if (!$row->logo) return 'No Image';
                    return '<img src="' . $row->logo . '" style="height:50px; border-radius:6px;">';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="btn-group">
                            <a href="'.route('certificate.edit', $row->id).'" class="btn btn-sm btn-info">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <button class="btn btn-sm btn-danger delete-btn" data-url="'.route('certificate.destroy', $row->id).'">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['image', 'action'])
                ->make(true);
        }

        return view('certificate.index');
    }

    public function create()
    {
        return view('certificate.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required|string',
            'logo'          => 'required|image|mimes:webp|max:2048',
            'display_order' => 'nullable|integer',
        ]);

        try {
            $logoPath = null;
            $logoFileId = null;
            if ($request->hasFile('logo')) {
                $upload = $this->imageKit->upload($request->file('logo'), 'certificates');
                if ($upload) {
                    $logoPath = $upload->url;
                    $logoFileId = $upload->fileId;
                }
            }

            DB::table('certificates')->insert([
                'title'         => $request->title,
                'full_title'         => $request->title,
                'logo'          => $logoPath,
                'logo_file_id'   => $logoFileId,
                'display_order' => $request->display_order ?? 0,
                'created_at'    => now(),
            ]);

            return redirect()->route('certificate.index')->with('success', 'Certificate created successfully');
        } catch (\Exception $e) {
            
            Log::error($e->getMessage());
            return back()->with('error', 'Failed create certificate: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $certificate = DB::table('certificates')->where('id', $id)->first();
        if (!$certificate) {
            return redirect()->route('certificate.index')->with('error', 'Certificate not found');
        }
        return view('certificate.edit', compact('certificate'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'         => 'required|string',
            'logo'          => 'nullable|image|mimes:webp|max:2048',
            'display_order' => 'nullable|integer',
        ]);

        try {
            $certificate = DB::table('certificates')->where('id', $id)->first();
            
            $updateData = [
                'title'         => $request->title,
                'full_title'         => $request->title,
                'display_order' => $request->display_order ?? 0,
            ];

            if ($request->hasFile('logo')) {
                // Delete old logo
                if ($certificate->logo_file_id) {
                    $this->imageKit->delete($certificate->logo_file_id);
                }
                // Upload new logo
                $upload = $this->imageKit->upload($request->file('logo'), 'certificates');
                if ($upload) {
                    $updateData['logo'] = $upload->url;
                    $updateData['logo_file_id'] = $upload->fileId;
                }
            }

            DB::table('certificates')->where('id', $id)->update($updateData);

            return redirect()->route('certificate.index')->with('success', 'Certificate updated successfully');

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed update certificate: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $certificate = DB::table('certificates')->where('id', $id)->first();

        if ($certificate && $certificate->logo_file_id) {
            $this->imageKit->delete($certificate->logo_file_id);
        }

        DB::table('certificates')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Certificate deleted!'
        ]);
    }
}
