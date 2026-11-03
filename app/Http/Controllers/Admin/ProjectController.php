<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Save all projects (batch update).
     */
    public function saveAll(Request $request)
    {
        $projects = $request->input('projects', []);
        $sectionKey = $request->input('section_key', 'our-work');
        
        \Illuminate\Support\Facades\Log::info('SaveAll called', ['section_key' => $sectionKey, 'projects_count' => count($projects)]);
        
        $section = Section::where('key', $sectionKey)->first();
        
        if (!$section) {
            \Illuminate\Support\Facades\Log::error('Section not found', ['key' => $sectionKey]);
            return response()->json([
                'success' => false,
                'message' => "Section '{$sectionKey}' not found"
            ], 404);
        }

        // Delete all existing project contents (pattern: project-list-1_project_*)
        $deleted = $section->contents()
            ->where('key', 'like', 'project-list-1_project_%')
            ->delete();
        
        \Illuminate\Support\Facades\Log::info('Deleted old projects', ['section_id' => $section->id, 'deleted_count' => $deleted]);

        // Create new projects
        foreach ($projects as $idx => $project) {
            $projectIndex = $idx + 1;
            $baseKey = "project-list-1_project_{$projectIndex}";
            $order = 5 + ($projectIndex * 5); // Start from order 5, increment by 5

            // Handle media
            $mediaSrc = $project['media_src'] ?? '';
            if (($project['media_type'] ?? 'video') === 'video' && !empty($project['video_url'])) {
                $mediaSrc = $this->convertToEmbedUrl($project['video_url']);
            }

            $contents = [
                ['key' => $baseKey . '_title', 'type' => 'text', 'value' => $project['title'] ?? '', 'order' => $order++],
                ['key' => $baseKey . '_desc', 'type' => 'textarea', 'value' => $project['desc'] ?? '', 'order' => $order++],
                ['key' => $baseKey . '_link', 'type' => 'url', 'value' => $project['link'] ?? '', 'order' => $order++],
                ['key' => $baseKey . '_media_src', 'type' => ($project['media_type'] ?? 'video') === 'video' ? 'video' : 'image', 'value' => $mediaSrc, 'order' => $order++],
                ['key' => $baseKey . '_media_type', 'type' => 'text', 'value' => $project['media_type'] ?? 'video', 'order' => $order++],
            ];

            foreach ($contents as $content) {
                $section->contents()->create($content);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'All projects saved successfully!',
            'count' => count($projects)
        ]);
    }

    /**
     * Upload project image.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'project-' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/projects', $filename);
            
            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully!',
                'path' => 'storage/projects/' . $filename,
                'url' => asset('storage/projects/' . $filename)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No image file provided'
        ], 400);
    }

    /**
     * Convert YouTube URL to embed URL.
     */
    private function convertToEmbedUrl($url)
    {
        if (empty($url)) return '';

        // Already embed URL
        if (strpos($url, 'youtube.com/embed/') !== false) {
            return $url;
        }

        $videoId = '';

        // youtu.be/VIDEO_ID
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $videoId = $matches[1];
        }
        // youtube.com/watch?v=VIDEO_ID
        elseif (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $videoId = $matches[1];
        }
        // youtube.com/shorts/VIDEO_ID
        elseif (preg_match('/youtube\.com\/shorts\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $videoId = $matches[1];
        }

        if ($videoId) {
            return "https://www.youtube.com/embed/{$videoId}";
        }

        return $url;
    }
}
