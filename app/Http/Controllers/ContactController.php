<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ContactController extends Controller
{
    /**
     * INDEX
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('contact_section')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <div class="btn-group">
                            <a href="'.route('contact.edit', $row->id).'" class="btn btn-sm btn-info">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <button class="btn btn-sm btn-danger delete-btn" data-url="'.route('contact.destroy', $row->id).'">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        // Count for singleton check (similar to gallery pattern if needed, or user adds manually)
        $count = DB::table('contact_section')->count();
        return view('contact.index', compact('count'));
    }

    /**
     * CREATE
     */
    public function create()
    {
        return view('contact.create');
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string',
            'subtitle'     => 'required|string',
            'description'  => 'required|string',
            'email'        => 'required|email',
            'phone'        => 'required|string',
            'address'      => 'required|string',
            'whatsapp'     => 'required|string',
            'map_embed_url' => 'required|string',
        ]);

        try {
            DB::table('contact_section')->insert([
                'title'        => $request->title,
                'subtitle'     => $request->subtitle,
                'description'  => $request->description,
                'email'        => $request->email,
                'phone'        => $request->phone,
                'address'      => $request->address,
                'whatsapp'     => $request->whatsapp,
                'map_embed_url'=> $request->map_embed_url,
            ]);

            return redirect()->route('contact.index')->with('success', 'Contact Section created successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed create contact section: ' . $e->getMessage());
        }
    }

    /**
     * EDIT
     */
    public function edit($id)
    {
        $contact = DB::table('contact_section')->where('id', $id)->first();
        if (!$contact) {
            return redirect()->route('contact.index')->with('error', 'Contact Section not found');
        }
        return view('contact.edit', compact('contact'));
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title'        => 'required|string',
            'subtitle'     => 'required|string',
            'description'  => 'required|string',
            'email'        => 'required|email',
            'phone'        => 'required|string',
            'address'      => 'required|string',
            'whatsapp'     => 'required|string',
            'map_embed_url' => 'required|string',
        ]);

        try {
            DB::table('contact_section')->where('id', $id)->update([
                'title'        => $request->title,
                'subtitle'     => $request->subtitle,
                'description'  => $request->description,
                'email'        => $request->email,
                'phone'        => $request->phone,
                'address'      => $request->address,
                'whatsapp'     => $request->whatsapp,
                'map_embed_url'=> $request->map_embed_url,
            ]);

            return redirect()->route('contact.index')->with('success', 'Contact Section updated successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed update contact section: ' . $e->getMessage());
        }
    }

    /**
     * DESTROY
     */
    public function destroy($id)
    {
        DB::table('contact_section')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact Section deleted!'
        ]);
    }
}
