<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class BlogController extends Controller
{
    /**
     * INDEX
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('blog_posts')->orderByDesc('created_at')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    if (!$row->image) return 'No Image';
                    $url = Storage::disk('nextjs')->url(str_replace('/uploads/', '', $row->image));
                    return '<img src="' . $url . '" style="height:50px; border-radius:6px;">';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="btn-group">
                            <a href="'.route('blog.edit', $row->id).'" class="btn btn-sm btn-info">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <button class="btn btn-sm btn-danger delete-btn" data-url="'.route('blog.destroy', $row->id).'">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['image', 'action'])
                ->make(true);
        }

        return view('blog.index');
    }

    /**
     * CREATE
     */
    public function create()
    {
        return view('blog.create');
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string',
            'slug'     => 'required|string',
            'date_str' => 'required|date',
            'author'   => 'required|string',
            'image'    => 'required|image|mimes:webp|max:2048',
            'excerpt'  => 'required|string',
            'content'  => 'required|string',
            // Tags can be array or string (comma separated)
            'tags'     => 'nullable', 
        ]);

        try {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $name = time().'_blog_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                Storage::disk('nextjs')->putFileAs('', $file, $name);
                $imagePath = '/uploads/'.$name;
            }

            // Process tags: if string "tag1, tag2", convert to json array ["tag1", "tag2"]
            $tags = [];
            if ($request->tags) {
                if (is_array($request->tags)) {
                    $tags = $request->tags;
                } else {
                    $tags = array_map('trim', explode(',', $request->tags));
                }
            }

            DB::table('blog_posts')->insert([
                'title'      => $request->title,
                'slug'       => Str::slug($request->slug), // Ensure slug format
                'excerpt'    => $request->excerpt,
                'content'    => $request->content,
                'date_str'   => $request->date_str,
                'author'     => $request->author,
                'tags'       => json_encode($tags),
                'image'      => $imagePath,
                'created_at' => now(),
            ]);

            return redirect()->route('blog.index')->with('success', 'Blog post created successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed create blog post: ' . $e->getMessage());
        }
    }

    /**
     * EDIT
     */
    public function edit($id)
    {
        $post = DB::table('blog_posts')->where('id', $id)->first();
        if (!$post) {
            return redirect()->route('blog.index')->with('error', 'Blog post not found');
        }
        
        // Convert tags json to string for display in input if needed or keep as is?
        // Usually tags input likes "tag1, tag2"
        $tags = json_decode($post->tags ?? '[]', true);
        $post->tags_string = implode(', ', $tags);

        return view('blog.edit', compact('post'));
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title'    => 'required|string',
            'slug'     => 'required|string',
            'date_str' => 'required|date',
            'author'   => 'required|string',
            'image'    => 'nullable|image|mimes:webp|max:2048',
            'excerpt'  => 'required|string',
            'content'  => 'required|string',
            'tags'     => 'nullable',
        ]);

        try {
            $post = DB::table('blog_posts')->where('id', $id)->first();
            
            $updateData = [
                'title'    => $request->title,
                'slug'     => Str::slug($request->slug),
                'excerpt'  => $request->excerpt,
                'content'  => $request->content,
                'date_str' => $request->date_str,
                'author'   => $request->author,
            ];

            // Process tags
            if ($request->tags) {
                if (is_array($request->tags)) {
                    $tags = $request->tags;
                } else {
                    $tags = array_map('trim', explode(',', $request->tags));
                }
                $updateData['tags'] = json_encode($tags);
            } else {
                $updateData['tags'] = json_encode([]);
            }

            if ($request->hasFile('image')) {
                // Delete old
                if ($post->image) {
                    $old = basename($post->image);
                    if (Storage::disk('nextjs')->exists($old)) {
                        Storage::disk('nextjs')->delete($old);
                    }
                }
                // Upload new
                $file = $request->file('image');
                $name = time().'_blog_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                Storage::disk('nextjs')->putFileAs('', $file, $name);
                $updateData['image'] = '/uploads/'.$name;
            }

            DB::table('blog_posts')->where('id', $id)->update($updateData);

            return redirect()->route('blog.index')->with('success', 'Blog post updated successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed update blog post: ' . $e->getMessage());
        }
    }

    /**
     * DESTROY
     */
    public function destroy($id)
    {
        $post = DB::table('blog_posts')->where('id', $id)->first();
        
        if ($post && $post->image) {
            $f = basename($post->image);
            if (Storage::disk('nextjs')->exists($f)) {
                Storage::disk('nextjs')->delete($f);
            }
        }

        DB::table('blog_posts')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Blog post deleted!'
        ]);
    }

    /**
     * GENERATE CONTENT (AJAX)
     */
    public function generate(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
            'title'   => 'required|string',
        ]);

        $apiKey = $request->api_key;
        $title = $request->title;
        
        // Prompt construction
        $prompt = "Act as an expert content strategist with deep knowledge in the field of: \"$title\". " .
          "Write a comprehensive, SEO-optimized blog post that adheres to E-E-A-T principles (Experience, Expertise, Authoritativeness, and Trustworthiness). \n\n" .
          "Guidelines for the content:\n" .
          "1. Tone: Professional yet engaging, providing unique insights or 'first-hand experience' feel.\n" .
          "2. Structure: Start with an engaging hook, use clear <h3> subheadings for readability, and include a strong concluding call-to-action.\n" .
          "3. SEO: Naturally incorporate relevant keywords and provide actionable value to the reader.\n" .
          "4. HTML: Ensure semantic tags are used correctly (p, h3, ul, li, strong).\n\n" .
          "Return the response strictly as valid JSON with this structure:\n" .
          "{\n" .
          "  \"excerpt\": \"A compelling meta-description (max 160 characters) that encourages clicks.\",\n" .
          "  \"content\": \"The full blog article in semantic HTML format.\",\n" .
          "  \"tags\": [\"keyword1\", \"keyword2\", \"keyword3\", \"keyword4\"]\n" .
          "}";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $json = $response->json();
                $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                // Debug log
                Log::info('Gemini Raw Response: ' . $text);

                // 1. Sanitize Markdown code blocks
                $text = preg_replace('/^```json/m', '', $text);
                $text = preg_replace('/^```/m', '', $text);
                
                // 2. Extract JSON object using Regex (capture from first { to last })
                if (preg_match('/\{[\s\S]*\}/', $text, $matches)) {
                    $text = $matches[0];
                }

                // 3. Decode
                $data = json_decode($text, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error('JSON Decode Error: ' . json_last_error_msg());
                    // Attempt to clean common invalid chars
                    // Sometimes AI sends control characters
                    $text = preg_replace('/[\x00-\x1F\x7F]/u', '', $text);
                    $data = json_decode($text, true);
                }

                if ($data) {
                    return response()->json([
                        'success' => true,
                        'data'    => $data
                    ]);
                } else {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Failed to parse AI response. JSON Error: ' . json_last_error_msg() . '. Raw: ' . substr($text, 0, 500)
                    ]);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Gemini API Error: ' . $response->body()]);
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()]);
        }
    }
}
