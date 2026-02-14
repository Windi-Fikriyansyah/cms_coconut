<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SampleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('samples')->orderBy('created_at', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('products', function($row) {
                    $products = json_decode($row->products, true);
                    if (is_array($products)) {
                        return implode(', ', $products);
                    }
                    return $row->products;
                })
                ->editColumn('created_at', function($row) {
                    return \Carbon\Carbon::parse($row->created_at)->format('d M Y H:i');
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="btn-group">
                            <a href="'.route('sample.show', $row->id).'" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button class="btn btn-sm btn-danger delete-btn" data-url="'.route('sample.destroy', $row->id).'">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('samples.index');
    }

    public function show($id)
    {
        $sample = DB::table('samples')->where('id', $id)->first();
        if (!$sample) abort(404);

        if ($sample->status === 'pending') {
            // Optional: automatically update status or keep it manual? 
            // Usually valid requests might stay pending until processed.
            // Let's just view it for now.
        }

        return view('samples.show', compact('sample'));
    }

    public function destroy($id)
    {
        DB::table('samples')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sample request deleted successfully!'
        ]);
    }
}
