<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

use App\Services\ImageKitService;

class AboutSectionController extends Controller
{
    protected $imageKit;

    public function __construct(ImageKitService $imageKit)
    {
        $this->imageKit = $imageKit;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = DB::table('about_section')->get();

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('image', function ($row) {
                    if (!$row->image) return 'No Image';
                    $images = json_decode($row->image, true);
                    $html = '';
                    foreach ($images as $img) {
                        $url = is_array($img) ? $img['url'] : $img;
                        $html .= '<img src="'.$url.'" style="height:50px;margin-right:5px;border-radius:6px;">';
                    }
                    return $html;
                })

                ->addColumn('action', function ($row) {

                    return '
                        <div class="btn-group">
                            <a href="'.route('about.edit',$row->id).'" class="btn btn-sm btn-info">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <button class="btn btn-sm btn-danger delete-btn"
                                data-url="'.route('about.destroy',$row->id).'">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    ';
                })

                ->rawColumns(['image','action'])
                ->make(true);
        }

        $count = DB::table('about_section')->count();

        return view('about_section.index',compact('count'));
    }

    public function create()
    {
        return view('about_section.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string',
        'description' => 'required|string',
        'button_text' => 'nullable|string',
        'button_link' => 'nullable|string',
        'image1' => 'nullable|image|mimes:webp|max:2048',
        'image2' => 'nullable|image|mimes:webp|max:2048',
        'image3' => 'nullable|image|mimes:webp|max:2048',
    ]);

    try {
        $images = [];

        for ($i = 1; $i <= 3; $i++) {
            if ($request->hasFile('image'.$i)) {
                $upload = $this->imageKit->upload($request->file('image'.$i), 'about_section');
                if ($upload) {
                    $images[] = [
                        'url' => $upload->url,
                        'fileId' => $upload->fileId
                    ];
                }
            }
        }

        DB::table('about_section')->insert([
            'title' => $request->title,
            'description' => $request->description,
            'image' => json_encode($images),
            'button_text' => $request->button_text,
            'button_link' => $request->button_link,
        ]);

        return redirect()->route('about.index')->with('success','About created successfully');

    } catch (\Exception $e) {
        Log::error($e->getMessage());
        return back()->with('error','Failed create about');
    }
}


    public function edit($id)
    {
        $about = DB::table('about_section')->where('id',$id)->first();

        return view('about_section.edit',compact('about'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'title' => 'required|string',
        'description' => 'required|string',
        'button_text' => 'nullable|string',
        'button_link' => 'nullable|string',
        'image1' => 'nullable|image|mimes:webp|max:2048',
        'image2' => 'nullable|image|mimes:webp|max:2048',
        'image3' => 'nullable|image|mimes:webp|max:2048',
    ]);

    $about = DB::table('about_section')->where('id', $id)->first();
    $data = $request->only(['title','description','button_text','button_link']);

    $images = json_decode($about->image ?? '[]', true);

    for ($i = 1; $i <= 3; $i++) {
        if ($request->hasFile('image'.$i)) {

            // hapus gambar lama jika ada
            if (isset($images[$i-1]) && is_array($images[$i-1]) && isset($images[$i-1]['fileId'])) {
                $this->imageKit->delete($images[$i-1]['fileId']);
            }

            $upload = $this->imageKit->upload($request->file('image'.$i), 'about_section');
            if ($upload) {
                $images[$i-1] = [
                    'url' => $upload->url,
                    'fileId' => $upload->fileId
                ];
            }
        }
    }

    $data['image'] = json_encode($images);

    DB::table('about_section')->where('id', $id)->update($data);

    return redirect()->route('about.index')->with('success', 'About updated!');
}

    public function destroy($id)
    {
        $about = DB::table('about_section')->where('id',$id)->first();

        if ($about->image){
            foreach (json_decode($about->image, true) as $img){
                if (is_array($img) && isset($img['fileId'])) {
                    $this->imageKit->delete($img['fileId']);
                }
            }
        }

        DB::table('about_section')->where('id',$id)->delete();

        return response()->json([
            'success'=>true,
            'message'=>'Deleted!'
        ]);
    }
}
