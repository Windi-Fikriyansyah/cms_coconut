<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('contact_messages')->orderBy('created_at', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function($row) {
                    return \Carbon\Carbon::parse($row->created_at)->format('d M Y H:i');
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="btn-group">
                            <a href="'.route('message.show', $row->id).'" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button class="btn btn-sm btn-danger delete-btn" data-url="'.route('message.destroy', $row->id).'">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('contact_messages.index');
    }

    public function show($id)
    {
        $message = DB::table('contact_messages')->where('id', $id)->first();
        if (!$message) abort(404);

        if ($message->status === 'unread') {
            DB::table('contact_messages')->where('id', $id)->update(['status' => 'read']);
        }

        return view('contact_messages.show', compact('message'));
    }

    public function destroy($id)
    {
        DB::table('contact_messages')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully!'
        ]);
    }
}
