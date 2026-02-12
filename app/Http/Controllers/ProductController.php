<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
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

                    $url = Storage::disk('nextjs')->url(
                        str_replace('/uploads/', '', $row->image)
                    );

                    return '<img src="' . $url . '" style="height:50px;border-radius:6px;">';
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
    private function triggerRevalidate($path = '/products')
    {
        try {
            $baseUrl = env('NEXT_PUBLIC_URL', 'http://localhost:3000');
            $secret = env('REVALIDATE_SECRET', 'coco_prime_secret_2024');
            $response = Http::post("{$baseUrl}/api/revalidate", [
                'secret' => $secret,
                'path'   => $path
            ]);
            if ($response->successful()) {
                Log::info("Revalidation success for path: {$path}");
            } else {
                Log::error("Revalidation failed for path: {$path}. Status: " . $response->status());
            }
        } catch (\Exception $e) {
            Log::error("Webhook error: " . $e->getMessage());
        }
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
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $name = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('uploads', $name, 'public');
                $imagePath = '/storage/uploads/' . $name;
            }

            // Why points — filter empty
            $whyPoints = array_values(array_filter($request->why_points ?? []));

            // Insert product
            $productId = DB::table('products')->insertGetId([
                'slug'             => $slug,
                'title'            => $request->title,
                'short_description'            => $request->short_description,
                'image'            => $imagePath,
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
                            $name = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                            Storage::disk('nextjs')->putFileAs('', $file, $name);
                            $detailImgs[] = ['url' => '/uploads/' . $name];
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

            $this->triggerRevalidate('/products');

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

            $data = [
                'slug'             => $slug,
                'title'            => $request->title,
                'short_description'            => $request->short_description,
                'meta_title'       => $request->meta_title,
                'meta_description' => $request->meta_description,
                'updated_at'      => now(),
            ];

            // Upload new product image
            if ($request->hasFile('image')) {
                // Delete old image
                if ($product->image) {
                    $oldFile = basename($product->image);
                    if (Storage::disk('nextjs')->exists($oldFile)) {
                        Storage::disk('nextjs')->delete($oldFile);
                    }
                }

                $file = $request->file('image');
                $name = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                Storage::disk('nextjs')->putFileAs('', $file, $name);
                $data['image'] = '/uploads/' . $name;
            }

            // Why points
            $whyPoints = array_values(array_filter($request->why_points ?? []));
            $data['why_points'] = json_encode($whyPoints);

            DB::table('products')->where('id', $id)->update($data);

            // Sync product details
            // Collect existing detail IDs to compare
            $existingDetailIds = DB::table('product_details')
                ->where('product_id', $id)
                ->pluck('id')
                ->toArray();

            $submittedDetailIds = array_filter($request->detail_id ?? []);

            // Delete removed details
            $toDelete = array_diff($existingDetailIds, $submittedDetailIds);
            if (!empty($toDelete)) {
                // Delete images for removed details
                $removedDetails = DB::table('product_details')->whereIn('id', $toDelete)->get();
                foreach ($removedDetails as $rd) {
                    if ($rd->image) {
                        $imgs = json_decode($rd->image, true);
                        if (is_array($imgs)) {
                            foreach ($imgs as $img) {
                                $f = basename($img['url'] ?? '');
                                if ($f && Storage::disk('nextjs')->exists($f)) {
                                    Storage::disk('nextjs')->delete($f);
                                }
                            }
                        }
                    }
                }
                DB::table('product_details')->whereIn('id', $toDelete)->delete();
            }

            // Update or insert details
            if ($request->detail_title) {
                foreach ($request->detail_title as $idx => $detailTitle) {
                    if (!$detailTitle) continue;

                    $detailId = $request->detail_id[$idx] ?? null;

                    // Handle detail images
                    $detailImgs = [];

                    // Keep existing images if editing
                    if ($detailId) {
                        $existingDetail = DB::table('product_details')->where('id', $detailId)->first();
                        if ($existingDetail && $existingDetail->image) {
                            $detailImgs = json_decode($existingDetail->image, true) ?: [];
                        }
                    }

                    // Upload new images
                    if ($request->hasFile("detail_images.$idx")) {
                        // Replace all images with new ones
                        // Delete old images first
                        foreach ($detailImgs as $img) {
                            $f = basename($img['url'] ?? '');
                            if ($f && Storage::disk('nextjs')->exists($f)) {
                                Storage::disk('nextjs')->delete($f);
                            }
                        }

                        $detailImgs = [];
                        foreach ($request->file("detail_images.$idx") as $file) {
                            $name = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                            Storage::disk('nextjs')->putFileAs('', $file, $name);
                            $detailImgs[] = ['url' => '/uploads/' . $name];
                        }
                    }

                    $detailData = [
                        'product_id'    => $id,
                        'title'         => $detailTitle,
                        'description'   => $request->detail_description[$idx] ?? null,
                        'image'         => json_encode($detailImgs),
                        'display_order' => $request->detail_display_order[$idx] ?? 0,
                    ];

                    if ($detailId && in_array($detailId, $existingDetailIds)) {
                        DB::table('product_details')->where('id', $detailId)->update($detailData);
                    } else {
                        DB::table('product_details')->insert($detailData);
                    }
                }
            }

            $this->triggerRevalidate('/products');
            $this->triggerRevalidate("/products/{$slug}");
            return redirect()->route('product.index')->with('success', 'Product updated successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }

    /**
     * DESTROY
     */
    public function destroy($id)
    {
        $product = DB::table('products')->where('id', $id)->first();

        // Delete product image
        if ($product->image) {
            $file = basename($product->image);
            if (Storage::disk('nextjs')->exists($file)) {
                Storage::disk('nextjs')->delete($file);
            }
        }

        // Delete product detail images
        $details = DB::table('product_details')->where('product_id', $id)->get();
        foreach ($details as $detail) {
            if ($detail->image) {
                $imgs = json_decode($detail->image, true);
                if (is_array($imgs)) {
                    foreach ($imgs as $img) {
                        $f = basename($img['url'] ?? '');
                        if ($f && Storage::disk('nextjs')->exists($f)) {
                            Storage::disk('nextjs')->delete($f);
                        }
                    }
                }
            }
        }

        DB::table('product_details')->where('product_id', $id)->delete();
        DB::table('products')->where('id', $id)->delete();
        $this->triggerRevalidate('/products');
        return response()->json([
            'success' => true,
            'message' => 'Product deleted!'
        ]);
    }
}
