<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('quotes')->orderBy('created_at', 'desc')->get();

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
                            <button class="btn btn-sm btn-danger delete-btn" data-url="'.route('quote.destroy', $row->id).'">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('quotes.index');
    }

    public function destroy($id)
    {
        DB::table('quotes')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Quote inquiry deleted successfully!'
        ]);
    }
}
