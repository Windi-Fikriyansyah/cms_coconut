<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CompanyStatsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('company_stats')->orderBy('display_order', 'asc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $editUrl = route('company-stats.edit', $row->id);
                    $deleteUrl = route('company-stats.destroy', $row->id);
                    $btn = '<div class="btn-group" role="group">
                            <a href="'.$editUrl.'" class="btn btn-info btn-sm"><i class="bi bi-pencil-square"></i></a>
                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-url="'.$deleteUrl.'"><i class="bi bi-trash"></i></button>
                            </div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('company_stats.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('company_stats.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'value' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'display_order' => 'required|integer',
        ]);

        DB::table('company_stats')->insert([
            'value' => $request->value,
            'label' => $request->label,
            'display_order' => $request->display_order,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('company-stats.index')
                        ->with('success', 'Company stat created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $stat = DB::table('company_stats')->where('id', $id)->first();
        
        if (!$stat) {
            return redirect()->route('company-stats.index')
                            ->with('error', 'Company stat not found.');
        }

        return view('company_stats.edit', compact('stat'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'value' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'display_order' => 'required|integer',
        ]);

        DB::table('company_stats')->where('id', $id)->update([
            'value' => $request->value,
            'label' => $request->label,
            'display_order' => $request->display_order,
            'updated_at' => now(),
        ]);

        return redirect()->route('company-stats.index')
                        ->with('success', 'Company stat updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('company_stats')->where('id', $id)->delete();

        return response()->json(['success' => true, 'message' => 'Company stat deleted successfully.']);
    }
}
