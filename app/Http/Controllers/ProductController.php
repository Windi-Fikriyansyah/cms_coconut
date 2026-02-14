<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

use App\Services\ImageKitService;

class ProductController extends Controller
{
    protected $imageKit;

    public function __construct(ImageKitService $imageKit)
    {
        $this->imageKit = $imageKit;
    }

    /**
     * INDEX — DataTable list
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = DB::table('products')->get();

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('image', function ($row) {
                    if (!$row->image) return 'No Image';
                    return '<img src="' . $row->image . '" style="height:50px; border-radius:6px;">';
                })

                ->addColumn('why_points_display', function ($row) {
                    if (!$row->why_points) return '-';

                    $points = json_decode($row->why_points, true);
                    if (!is_array($points)) return '-';

                    $html = '<ul class="mb-0 ps-3" style="font-size:12px;">';
                    foreach (array_slice($points, 0, 2) as $point) {
                        $html .= '<li>' . e($point) . '</li>';
                    }
                    if (count($points) > 2) {
                        $html .= '<li>... +' . (count($points) - 2) . ' lagi</li>';
                    }
                    $html .= '</ul>';

                    return $html;
                })

                ->addColumn('action', function ($row) {
                    return '
                        <div class="btn-group">
                            <a href="' . route('product.edit', $row->id) . '" class="btn btn-sm btn-info">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <button class="btn btn-sm btn-danger delete-btn"
                                data-url="' . route('product.destroy', $row->id) . '">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    ';
                })

                ->rawColumns(['image', 'why_points_display', 'action'])
                ->make(true);
        }

        return view('product.index');
    }

    /**
     * CREATE
     */
    public function create()
    {
        return view('product.create');
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'            => 'required|string',
            'short_description'            => 'required|string',
            'image'            => 'nullable|image|mimes:webp|max:2048',
            'why_points'       => 'nullable|array',
            'why_points.*'     => 'nullable|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            // product detail
            'detail_title'           => 'nullable|array',
            'detail_title.*'         => 'nullable|string',
            'detail_description'     => 'nullable|array',
            'detail_description.*'   => 'nullable|string',
            'detail_images'          => 'nullable|array',
            'detail_images.*'        => 'nullable|array',
            'detail_images.*.*'      => 'nullable|image|mimes:webp|max:2048',
            'detail_display_order'   => 'nullable|array',
            'detail_display_order.*' => 'nullable|integer',
        ]);

        try {
            // Slug
            $slug = Str::slug($request->title);

            // Upload product image
            $imagePath = null;
            $imageFileId = null;
            if ($request->hasFile('image')) {
                $upload = $this->imageKit->upload($request->file('image'), 'products');
                if ($upload) {
                    $imagePath = $upload->url;
                    $imageFileId = $upload->fileId;
                }
            }

            // Why points — filter empty
            $whyPoints = array_values(array_filter($request->why_points ?? []));

            // Insert product
            $productId = DB::table('products')->insertGetId([
                'slug'             => $slug,
                'title'            => $request->title,
                'short_description'            => $request->short_description,
                'image'            => $imagePath,
                'image_file_id'    => $imageFileId,
                'why_points'       => json_encode($whyPoints),
                'meta_title'       => $request->meta_title,
                'meta_description' => $request->meta_description,
                'updated_at'      => now(),
                'created_at'      => now(),
            ]);

            // Insert product details
            if ($request->detail_title) {
                foreach ($request->detail_title as $idx => $detailTitle) {
                    if (!$detailTitle) continue;

                    // Upload detail images
                    $detailImgs = [];
                    if ($request->hasFile("detail_images.$idx")) {
                        foreach ($request->file("detail_images.$idx") as $file) {
                            $upload = $this->imageKit->upload($file, 'products/details');
                            if ($upload) {
                                $detailImgs[] = [
                                    'url' => $upload->url,
                                    'fileId' => $upload->fileId
                                ];
                            }
                        }
                    }

                    DB::table('product_details')->insert([
                        'product_id'    => $productId,
                        'title'         => $detailTitle,
                        'description'   => $request->detail_description[$idx] ?? null,
                        'image'         => json_encode($detailImgs),
                        'display_order' => $request->detail_display_order[$idx] ?? 0,
                    ]);
                }
            }

            return redirect()->route('product.index')->with('success', 'Product created successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }

    /**
     * EDIT
     */
    public function edit($id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        $details = DB::table('product_details')
            ->where('product_id', $id)
            ->orderBy('display_order')
            ->get();

        return view('product.edit', compact('product', 'details'));
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title'            => 'required|string',
            'short_description'            => 'required|string',
            'image'            => 'nullable|image|mimes:webp|max:2048',
            'why_points'       => 'nullable|array',
            'why_points.*'     => 'nullable|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            // product detail
            'detail_id'              => 'nullable|array',
            'detail_title'           => 'nullable|array',
            'detail_title.*'         => 'nullable|string',
            'detail_description'     => 'nullable|array',
            'detail_description.*'   => 'nullable|string',
            'detail_images'          => 'nullable|array',
            'detail_images.*'        => 'nullable|array',
            'detail_images.*.*'      => 'nullable|image|mimes:webp|max:2048',
            'detail_display_order'   => 'nullable|array',
            'detail_display_order.*' => 'nullable|integer',
        ]);

        
        try {
            $product = DB::table('products')->where('id', $id)->first();
            $slug = Str::slug($request->title);

            $updateData = [
                'slug'             => $slug,
                'title'            => $request->title,
                'short_description'            => $request->short_description,
                'meta_title'       => $request->meta_title,
                'meta_description' => $request->meta_description,
                'updated_at'      => now(),
            ];

            // Upload new product image
            if ($request->hasFile('image')) {
                if ($product->image_file_id) {
                    $this->imageKit->delete($product->image_file_id);
                }
                $upload = $this->imageKit->upload($request->file('image'), 'products');
                if (!$upload) {
    throw new \Exception('Upload ke ImageKit gagal');
}
                if ($upload) {
                    $updateData['image'] = $upload->url;
                    $updateData['image_file_id'] = $upload->fileId;
                }
            }

            // Why points
            $whyPoints = array_values(array_filter($request->why_points ?? []));
            $updateData['why_points'] = json_encode($whyPoints);

            DB::table('products')->where('id', $id)->update($updateData);

            // Sync product details
            $existingDetails = DB::table('product_details')
                ->where('product_id', $id)
                ->get();

            $submittedDetailIds = array_filter($request->detail_id ?? []);
            $keepDetailIds = [];

            if ($request->detail_title) {
                foreach ($request->detail_title as $idx => $detailTitle) {
                    if (!$detailTitle) continue;

                    $detailId = $request->detail_id[$idx] ?? null;
                    $newDetailImgs = [];

                    // Get existing images for this detail if it's an update
                    $oldDetail = null;
                    if ($detailId) {
                        $oldDetail = $existingDetails->firstWhere('id', $detailId);
                        if ($oldDetail && $oldDetail->image) {
                            $newDetailImgs = json_decode($oldDetail->image, true) ?: [];
                        }
                    }

                    // Upload new images
                    if ($request->hasFile("detail_images.$idx")) {
                        // Delete old images associated with this detail if new ones are uploaded
                        foreach ($newDetailImgs as $img) {
                            if (isset($img['fileId'])) {
                                $this->imageKit->delete($img['fileId']);
                            }
                        }
                        $newDetailImgs = []; // Reset for new uploads

                        foreach ($request->file("detail_images.$idx") as $file) {
                            $upload = $this->imageKit->upload($file, 'products/details');
                            if ($upload) {
                                $newDetailImgs[] = [
                                    'url' => $upload->url,
                                    'fileId' => $upload->fileId
                                ];
                            }
                        }
                    }

                    $detailData = [
                        'product_id'    => $id,
                        'title'         => $detailTitle,
                        'description'   => $request->detail_description[$idx] ?? null,
                        'image'         => json_encode($newDetailImgs),
                        'display_order' => $request->detail_display_order[$idx] ?? 0,
                    ];

                    if ($detailId && $oldDetail) {
                        DB::table('product_details')->where('id', $detailId)->update($detailData);
                        $keepDetailIds[] = $detailId;
                    } else {
                        $newId = DB::table('product_details')->insertGetId($detailData);
                        $keepDetailIds[] = $newId;
                    }
                }
            }

            // Delete details that were not in the submitted list
            foreach ($existingDetails as $ed) {
                if (!in_array($ed->id, $keepDetailIds)) {
                    $oldImgs = json_decode($ed->image ?? '[]', true);
                    foreach ($oldImgs as $oi) {
                        if (isset($oi['fileId'])) {
                            $this->imageKit->delete($oi['fileId']);
                        }
                    }
                    DB::table('product_details')->where('id', $ed->id)->delete();
                }
            }

            return redirect()->route('product.index')->with('success', 'Product updated successfully');
        } catch (\Exception $e) {
            // dd($e);
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }

    /**
     * PAGE SETTINGS — Update products_page metadata
     */
    public function pageIndex()
    {
        $page = DB::table('products_page')->first();
        return view('product.page_settings', compact('page'));
    }

    public function pageUpdate(Request $request)
    {
        $request->validate([
            'hero_title' => 'required|string',
            'hero_description' => 'required|string',
            'hero_badge' => 'nullable|string',
            'hero_image' => 'nullable|image|mimes:webp|max:2048',
            'cta_description' => 'required|string',
            'cta_whatsapp' => 'required|string',
            'cta_email' => 'required|email',
            'cta_whatsapp_label' => 'required|string',
            'cta_email_label' => 'required|string',
        ]);

        try {
            $page = DB::table('products_page')->first();
            $data = $request->only([
                'hero_title', 'hero_description', 'hero_badge',
                'cta_description', 'cta_whatsapp', 'cta_email',
                'cta_whatsapp_label', 'cta_email_label'
            ]);

            if ($request->hasFile('hero_image')) {
                // Delete old image if exists
                if ($page && isset($page->hero_image_file_id) && $page->hero_image_file_id) {
                    $this->imageKit->delete($page->hero_image_file_id);
                }

                $upload = $this->imageKit->upload($request->file('hero_image'), 'products/page');
                if ($upload) {
                    $data['hero_image'] = $upload->url;
                    $data['hero_image_file_id'] = $upload->fileId;
                }
            }

            if ($page) {
                DB::table('products_page')->where('id', $page->id)->update($data);
            } else {
                DB::table('products_page')->insert($data);
            }

            return redirect()->route('product.index')->with('success', 'Product page settings updated successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to update product page settings: ' . $e->getMessage());
        }
    }

    /**
     * DESTROY
     */
    public function destroy($id)
    {
        $product = DB::table('products')->where('id', $id)->first();

        // Delete product image
        if ($product->image_file_id) {
            $this->imageKit->delete($product->image_file_id);
        }

        // Delete product detail images
        $details = DB::table('product_details')->where('product_id', $id)->get();
        foreach ($details as $detail) {
            if ($detail->image) {
                $imgs = json_decode($detail->image, true);
                if (is_array($imgs)) {
                    foreach ($imgs as $img) {
                        if (isset($img['fileId'])) {
                            $this->imageKit->delete($img['fileId']);
                        }
                    }
                }
            }
        }

        DB::table('product_details')->where('product_id', $id)->delete();
        DB::table('products')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted!'
        ]);
    }
}
