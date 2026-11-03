<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\SectionContent;
use App\Models\Media;
use App\Rules\MediaUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class ContentController extends Controller
{
    /**
     * Update content for a section
     */
    public function updateContent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'section_key' => 'required|string',
            'content_key' => 'required|string',
            'value' => 'nullable',
            'type' => 'required|in:text,textarea,rich-text,image,video,url,json',
            'file' => ['nullable', new MediaUpload],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Get or create section
            $section = Section::firstOrCreate(
                ['key' => $request->section_key],
                [
                    'name' => ucfirst(str_replace('-', ' ', $request->section_key)),
                    'type' => 'default',
                    'is_active' => true,
                ]
            );

            // Handle image/video upload if type is image or video
            $mediaId = null;
            $value = $request->value;

            if (in_array($request->type, ['image', 'video']) && $request->hasFile('file')) {
                // Delete old media if exists
                $existingContent = $section->contents()->where('key', $request->content_key)->first();
                if ($existingContent && $existingContent->media_id) {
                    $this->deleteOldMedia($existingContent->media_id);
                }

                $media = $this->uploadMedia($request->file('file'));
                $mediaId = $media->id;
                $value = 'storage/' . $media->path;
            }

            // Update or create content
            $content = $section->contents()->updateOrCreate(
                ['key' => $request->content_key],
                [
                    'type' => $request->type,
                    'value' => $value,
                    'media_id' => $mediaId,
                ]
            );

            // Load media relation
            $content->load('media');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Content updated successfully',
                'data' => $content,
                'file_url' => $value
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('ContentController updateContent error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan konten, coba lagi.'
            ], 500);
        }
    }

    /**
     * Upload single file
     */
    public function uploadFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => ['required', new MediaUpload],
            'type' => 'nullable|in:image,video',
            'section_key' => 'nullable|string',
            'content_key' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $media = $this->uploadMedia($request->file('file'));

            // If section_key and content_key provided, link to content
            if ($request->section_key && $request->content_key) {
                $section = Section::where('key', $request->section_key)->first();
                if ($section) {
                    $content = $section->contents()->where('key', $request->content_key)->first();
                    if ($content) {
                        // Delete old media
                        if ($content->media_id) {
                            $this->deleteOldMedia($content->media_id);
                        }
                        $content->update([
                            'value' => 'storage/' . $media->path,
                            'media_id' => $media->id,
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'data' => $media,
                'url' => asset('storage/' . $media->path),
                'path' => 'storage/' . $media->path
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('ContentController uploadFile error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah file, coba lagi.'
            ], 500);
        }
    }

    /**
     * Get all media files (for media library)
     */
    public function getMediaList(Request $request)
    {
        try {
            $query = Media::query();

            if ($request->type) {
                $query->where('type', $request->type);
            }

            if ($request->search) {
                $query->where('original_name', 'like', '%' . $request->search . '%');
            }

            $media = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 20);

            // Add URL to each media item
            $media->getCollection()->transform(function ($item) {
                $item->url = asset('storage/' . $item->path);
                return $item;
            });

            return response()->json([
                'success' => true,
                'data' => $media
            ]);

        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar media, coba lagi.'
            ], 500);
        }
    }

    /**
     * Delete old media file
     */
    private function deleteOldMedia($mediaId)
    {
        try {
            $media = Media::find($mediaId);
            if ($media) {
                if (Storage::disk('public')->exists($media->path)) {
                    Storage::disk('public')->delete($media->path);
                }
                $media->delete();
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to delete old media: ' . $e->getMessage());
        }
    }

    /**
     * Update multiple contents in batch
     */
    public function batchUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'files' => 'nullable|array',
            'files.*' => [new MediaUpload],
        ]);

        if ($validator->fails()) {
            // The editor shows `message` to the admin, so make it the actual reason.
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Decode JSON contents if sent as string
            $contents = $request->contents;
            if (is_string($contents)) {
                $contents = json_decode($contents, true);
            }

            // Simple validation
            if (!$request->section_key || empty($contents)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Section key and contents are required'
                ], 422);
            }

            \Illuminate\Support\Facades\Log::info('BatchUpdate called', [
                'section_key' => $request->section_key,
                'contents_count' => count($contents),
                'has_files' => $request->hasAny(['files']),
            ]);

            DB::beginTransaction();

            // Get or create section
            $section = Section::firstOrCreate(
                ['key' => $request->section_key],
                [
                    'name' => ucfirst(str_replace('-', ' ', $request->section_key)),
                    'type' => 'default',
                    'is_active' => true,
                ]
            );

            $updated = [];
            $uploadedFiles = [];
            $mediaToDelete = []; // Collect media IDs to delete after all updates

            // Handle file uploads first
            // Files can come as: files[fieldname] which Laravel parses as ['files' => ['fieldname' => UploadedFile]]
            $filesArray = $request->file('files');
            
            if (!empty($filesArray) && is_array($filesArray)) {
                foreach ($filesArray as $fieldName => $file) {
                    // Skip if not a valid file
                    if (!$file || !($file instanceof \Illuminate\Http\UploadedFile)) {
                        continue;
                    }

                    $media = $this->uploadMedia($file);
                    $uploadedFiles[$fieldName] = $media;

                    \Illuminate\Support\Facades\Log::info('File uploaded', [
                        'field' => $fieldName,
                        'original_name' => $file->getClientOriginalName(),
                        'path' => $media->path,
                    ]);
                }
            }

            $processedKeys = []; // Track processed keys to prevent duplicates
            
            // Pre-process: Clear old pricing features before saving new ones
            // This prevents duplicate features when indices change
            $pricingPlansToClean = [];
            foreach ($contents as $contentData) {
                if (!empty($contentData['key']) && preg_match('/plan_(basic|standard|premium)_feature_\d+/', $contentData['key'])) {
                    $matches = [];
                    preg_match('/plan_(basic|standard|premium)_/', $contentData['key'], $matches);
                    if (!empty($matches[1])) {
                        $pricingPlansToClean[$matches[1]] = true;
                    }
                }
            }
            
            // Delete all existing features for plans that are being updated
            foreach (array_keys($pricingPlansToClean) as $planId) {
                $deletedCount = $section->contents()
                    ->where('key', 'like', '%plan_' . $planId . '_feature_%')
                    ->delete();
                
                \Illuminate\Support\Facades\Log::info('Cleared old pricing features', [
                    'plan_id' => $planId,
                    'deleted_count' => $deletedCount,
                ]);
            }
            
            // Pre-process: Clear old about-us list items before saving new ones
            // This prevents duplicate items when indices change after deletion
            $hasAboutListItems = false;
            foreach ($contents as $contentData) {
                if (!empty($contentData['key']) && preg_match('/_list_item_\d+$/', $contentData['key'])) {
                    $hasAboutListItems = true;
                    break;
                }
            }
            
            if ($hasAboutListItems) {
                // Delete all existing list_item_X entries for this section
                $deletedCount = $section->contents()
                    ->where('key', 'like', '%_list_item_%')
                    ->delete();
                
                \Illuminate\Support\Facades\Log::info('Cleared old about list items before save', [
                    'deleted_count' => $deletedCount,
                ]);
            }
            
            // Pre-process: Clear old vision/mission list items before saving new ones
            $hasVisionList = false;
            $hasMissionList = false;
            foreach ($contents as $contentData) {
                if (!empty($contentData['key'])) {
                    if (preg_match('/_vision_list_\d+$/', $contentData['key'])) {
                        $hasVisionList = true;
                    }
                    if (preg_match('/_mission_list_\d+$/', $contentData['key'])) {
                        $hasMissionList = true;
                    }
                }
            }
            
            if ($hasVisionList) {
                $deletedCount = $section->contents()
                    ->where('key', 'like', '%_vision_list_%')
                    ->delete();
                
                \Illuminate\Support\Facades\Log::info('Cleared old vision list items', [
                    'deleted_count' => $deletedCount,
                ]);
            }
            
            if ($hasMissionList) {
                $deletedCount = $section->contents()
                    ->where('key', 'like', '%_mission_list_%')
                    ->delete();
                
                \Illuminate\Support\Facades\Log::info('Cleared old mission list items', [
                    'deleted_count' => $deletedCount,
                ]);
            }
            
            foreach ($contents as $contentData) {
                // Skip if no key
                if (empty($contentData['key'])) {
                    continue;
                }

                $contentType = $contentData['type'] ?? 'text';
                
                // Skip if this key was already processed (prevent duplicates)
                $keyForTracking = $contentData['key'] . '_' . $contentType;
                if (isset($processedKeys[$keyForTracking]) && !in_array($contentType, ['delete', 'delete_service', 'delete_project', 'delete_member', 'delete_client_logo', 'delete_tool', 'delete_process', 'delete_achievement', 'delete_service_benefit', 'delete_service_process', 'delete_service_faq'])) {
                    \Illuminate\Support\Facades\Log::warning('Duplicate key skipped', ['key' => $contentData['key'], 'type' => $contentType]);
                    continue;
                }
                $processedKeys[$keyForTracking] = true;

                // Handle delete operations
                if ($contentType === 'delete') {
                    $deleteKey = str_replace('delete_image_', 'image_', $contentData['key']);
                    $existingContent = $section->contents()->where('key', 'like', '%' . $deleteKey . '%')->first();
                    if ($existingContent && $existingContent->media_id) {
                        $mediaToDelete[] = $existingContent->media_id;
                        $existingContent->update(['value' => '', 'media_id' => null]);
                    }
                    continue;
                }

                // Handle reset to default operations
                if ($contentType === 'reset') {
                    // Find existing content and delete its media if exists
                    $existingContent = $section->contents()->where('key', $contentData['key'])->first();
                    if ($existingContent && $existingContent->media_id) {
                        $mediaToDelete[] = $existingContent->media_id;
                    }
                    
                    // Update content with default image path and remove media reference
                    $section->contents()->updateOrCreate(
                        ['key' => $contentData['key']],
                        [
                            'type' => 'image',
                            'value' => $contentData['value'], // This is the default image path
                            'media_id' => null,
                        ]
                    );
                    
                    \Illuminate\Support\Facades\Log::info('Reset to default', [
                        'key' => $contentData['key'],
                        'default_value' => $contentData['value'],
                    ]);
                    continue;
                }

                // Handle service deletion
                if ($contentType === 'delete_service') {
                    $serviceId = $contentData['value'];
                    
                    // Get all service contents first to collect their media for deletion
                    $serviceContents = $section->contents()
                        ->where('key', 'like', '%service_' . $serviceId . '%')
                        ->get();
                    
                    // Collect media IDs for deletion after transaction
                    foreach ($serviceContents as $serviceContent) {
                        if ($serviceContent->media_id) {
                            $mediaToDelete[] = $serviceContent->media_id;
                        }
                    }
                    
                    // Then delete the content records
                    $deletedCount = $section->contents()
                        ->where('key', 'like', '%service_' . $serviceId . '%')
                        ->delete();
                    
                    \Illuminate\Support\Facades\Log::info('Service deleted', [
                        'service_id' => $serviceId,
                        'deleted_count' => $deletedCount,
                    ]);
                    continue;
                }

                // Handle project deletion
                if ($contentType === 'delete_project') {
                    $projectId = $contentData['value'];
                    // Get all content related to this project and collect their media
                    $projectContents = $section->contents()
                        ->where('key', 'like', '%project_' . $projectId . '%')
                        ->get();
                    
                    foreach ($projectContents as $projectContent) {
                        if ($projectContent->media_id) {
                            $mediaToDelete[] = $projectContent->media_id;
                        }
                    }
                    
                    $deletedCount = $section->contents()
                        ->where('key', 'like', '%project_' . $projectId . '%')
                        ->delete();
                    
                    \Illuminate\Support\Facades\Log::info('Project deleted', [
                        'project_id' => $projectId,
                        'deleted_count' => $deletedCount,
                    ]);
                    continue;
                }

                // Handle team member deletion
                if ($contentType === 'delete_member') {
                    $memberIndex = (int) $contentData['value'];
                    
                    // Get all content related to this team member and collect their media
                    $memberContents = $section->contents()
                        ->where('key', 'like', '%member_' . $memberIndex . '_%')
                        ->get();
                    
                    foreach ($memberContents as $memberContent) {
                        if ($memberContent->media_id) {
                            $mediaToDelete[] = $memberContent->media_id;
                        }
                    }
                    
                    $deletedCount = $section->contents()
                        ->where('key', 'like', '%member_' . $memberIndex . '_%')
                        ->delete();
                    
                    \Illuminate\Support\Facades\Log::info('Team member deleted', [
                        'member_index' => $memberIndex,
                        'deleted_count' => $deletedCount,
                    ]);
                    
                    // Reindex: shift all members with higher index down by 1
                    // Find max member index
                    $maxIndex = 0;
                    $allMemberContents = $section->contents()
                        ->where('key', 'like', '%member_%')
                        ->get();
                    
                    foreach ($allMemberContents as $content) {
                        if (preg_match('/member_(\d+)_/', $content->key, $matches)) {
                            $maxIndex = max($maxIndex, (int) $matches[1]);
                        }
                    }
                    
                    // Shift down all members with index > deleted index
                    for ($i = $memberIndex + 1; $i <= $maxIndex; $i++) {
                        $newIndex = $i - 1;
                        $contentsToUpdate = $section->contents()
                            ->where('key', 'like', '%member_' . $i . '_%')
                            ->get();
                        
                        foreach ($contentsToUpdate as $content) {
                            $newKey = preg_replace('/member_' . $i . '_/', 'member_' . $newIndex . '_', $content->key);
                            $content->update(['key' => $newKey]);
                        }
                        
                        \Illuminate\Support\Facades\Log::info('Reindexed team member', [
                            'from_index' => $i,
                            'to_index' => $newIndex,
                        ]);
                    }
                    
                    continue;
                }

                // Handle client logo deletion
                if ($contentType === 'delete_client_logo') {
                    $logoIndex = $contentData['value'];
                    
                    // Use exact key match to avoid matching similar keys (e.g., 0 matching 10)
                    $exactKey = 'client-logos-2_client_logo_' . $logoIndex;
                    
                    // Get content with exact key and collect media
                    $logoContent = $section->contents()->where('key', $exactKey)->first();
                    
                    if ($logoContent) {
                        if ($logoContent->media_id) {
                            $mediaToDelete[] = $logoContent->media_id;
                        }
                        $logoContent->delete();
                        
                        \Illuminate\Support\Facades\Log::info('Client logo deleted', [
                            'logo_index' => $logoIndex,
                            'key' => $exactKey,
                        ]);
                    }
                    continue;
                }

                // Handle about list item deletion
                if ($contentType === 'delete_list_item') {
                    $itemIndex = $contentData['value'];
                    
                    // Extract item-id prefix from key (e.g., about-us-1_delete_list_item_0 -> about-us-1)
                    $keyParts = explode('_delete_list_item_', $contentData['key']);
                    $itemPrefix = $keyParts[0] ?? '';
                    
                    // Build the exact key for the list item
                    $exactKey = $itemPrefix . '_list_item_' . $itemIndex;
                    
                    // Delete the content
                    $deletedCount = $section->contents()->where('key', $exactKey)->delete();
                    
                    \Illuminate\Support\Facades\Log::info('List item deleted', [
                        'item_index' => $itemIndex,
                        'key' => $exactKey,
                        'deleted_count' => $deletedCount,
                    ]);
                    continue;
                }

                // Handle vision/mission list item deletion
                if ($contentType === 'delete_vm_list') {
                    $value = $contentData['value']; // format: vision_0 or mission_1
                    $parts = explode('_', $value);
                    $vmType = $parts[0] ?? '';
                    $itemIndex = $parts[1] ?? '';
                    
                    // Build the exact key (e.g., vision-mission-1_vision_list_0)
                    $exactKey = 'vision-mission-1_' . $vmType . '_list_' . $itemIndex;
                    
                    // Delete the content
                    $deletedCount = $section->contents()->where('key', $exactKey)->delete();
                    
                    \Illuminate\Support\Facades\Log::info('VM list item deleted', [
                        'type' => $vmType,
                        'index' => $itemIndex,
                        'key' => $exactKey,
                        'deleted_count' => $deletedCount,
                    ]);
                    continue;
                }

                // Handle pricing feature deletion
                if ($contentType === 'delete_pricing_feature') {
                    $value = $contentData['value']; // format: basic_1 or standard_2
                    $parts = explode('_', $value);
                    $planId = $parts[0] ?? '';
                    $featureIndex = $parts[1] ?? '';
                    
                    // Build the exact key (e.g., pricing-plans_plan_basic_feature_1)
                    $exactKey = 'pricing-plans_plan_' . $planId . '_feature_' . $featureIndex;
                    
                    // Delete the content
                    $deletedCount = $section->contents()->where('key', $exactKey)->delete();
                    
                    \Illuminate\Support\Facades\Log::info('Pricing feature deleted', [
                        'plan_id' => $planId,
                        'feature_index' => $featureIndex,
                        'key' => $exactKey,
                        'deleted_count' => $deletedCount,
                    ]);
                    continue;
                }

                // Handle tool deletion
                if ($contentType === 'delete_tool') {
                    $toolId = $contentData['value'];
                    
                    // Build the key pattern (e.g., tools-list-1_tool_0_*)
                    $keyPattern = '%tool_' . $toolId . '_%';
                    
                    // Get tool contents to collect media for deletion
                    $toolContents = $section->contents()->where('key', 'like', $keyPattern)->get();
                    
                    foreach ($toolContents as $toolContent) {
                        if ($toolContent->media_id) {
                            $mediaToDelete[] = $toolContent->media_id;
                        }
                    }
                    
                    // Delete all fields for this tool
                    $deletedCount = $section->contents()->where('key', 'like', $keyPattern)->delete();
                    
                    \Illuminate\Support\Facades\Log::info('Tool deleted', [
                        'tool_id' => $toolId,
                        'key_pattern' => $keyPattern,
                        'deleted_count' => $deletedCount,
                    ]);
                    continue;
                }

                // Handle process step deletion
                if ($contentType === 'delete_process') {
                    $processId = $contentData['value'];
                    
                    // Build the key pattern (e.g., process-list-1_step_0_*)
                    $keyPattern = '%step_' . $processId . '_%';
                    
                    // Delete all fields for this process step
                    $deletedCount = $section->contents()->where('key', 'like', $keyPattern)->delete();
                    
                    \Illuminate\Support\Facades\Log::info('Process step deleted', [
                        'process_id' => $processId,
                        'key_pattern' => $keyPattern,
                        'deleted_count' => $deletedCount,
                    ]);
                    continue;
                }

                // Handle achievement deletion
                if ($contentType === 'delete_achievement') {
                    $achievementId = $contentData['value'];
                    
                    // Build the key pattern (e.g., achievement-list-1_ach_0_*)
                    $keyPattern = '%ach_' . $achievementId . '_%';
                    
                    // Delete all fields for this achievement
                    $deletedCount = $section->contents()->where('key', 'like', $keyPattern)->delete();
                    
                    \Illuminate\Support\Facades\Log::info('Achievement deleted', [
                        'achievement_id' => $achievementId,
                        'key_pattern' => $keyPattern,
                        'deleted_count' => $deletedCount,
                    ]);
                    continue;
                }

                // Handle service benefit deletion
                if ($contentType === 'delete_service_benefit') {
                    // Key format: delete_service_benefit_SERVICEID_INDEX
                    $keyParts = explode('_', $contentData['key']);
                    $serviceId = $keyParts[3] ?? '';
                    $benefitIndex = $keyParts[4] ?? '';
                    
                    if ($serviceId && $benefitIndex) {
                        $keyPattern = 'services-list-1_service_' . $serviceId . '_benefit_' . $benefitIndex;
                        $deletedCount = $section->contents()->where('key', $keyPattern)->delete();
                        
                        \Illuminate\Support\Facades\Log::info('Service benefit deleted', [
                            'service_id' => $serviceId,
                            'benefit_index' => $benefitIndex,
                            'deleted_count' => $deletedCount,
                        ]);
                    }
                    continue;
                }

                // Handle service process deletion
                if ($contentType === 'delete_service_process') {
                    // Key format: delete_service_process_SERVICEID_INDEX
                    $keyParts = explode('_', $contentData['key']);
                    $serviceId = $keyParts[3] ?? '';
                    $processIndex = $keyParts[4] ?? '';
                    
                    if ($serviceId && $processIndex) {
                        // Delete icon, title, and desc for this process
                        $keyPatterns = [
                            'services-list-1_service_' . $serviceId . '_process_' . $processIndex . '_icon',
                            'services-list-1_service_' . $serviceId . '_process_' . $processIndex . '_title',
                            'services-list-1_service_' . $serviceId . '_process_' . $processIndex . '_desc',
                        ];
                        $deletedCount = $section->contents()->whereIn('key', $keyPatterns)->delete();
                        
                        \Illuminate\Support\Facades\Log::info('Service process deleted', [
                            'service_id' => $serviceId,
                            'process_index' => $processIndex,
                            'deleted_count' => $deletedCount,
                        ]);
                    }
                    continue;
                }

                // Handle service FAQ deletion
                if ($contentType === 'delete_service_faq') {
                    // Key format: delete_service_faq_SERVICEID_INDEX
                    $keyParts = explode('_', $contentData['key']);
                    $serviceId = $keyParts[3] ?? '';
                    $faqIndex = $keyParts[4] ?? '';
                    
                    if ($serviceId && $faqIndex) {
                        // Delete question and answer for this FAQ
                        $keyPatterns = [
                            'services-list-1_service_' . $serviceId . '_faq_' . $faqIndex . '_question',
                            'services-list-1_service_' . $serviceId . '_faq_' . $faqIndex . '_answer',
                        ];
                        $deletedCount = $section->contents()->whereIn('key', $keyPatterns)->delete();
                        
                        \Illuminate\Support\Facades\Log::info('Service FAQ deleted', [
                            'service_id' => $serviceId,
                            'faq_index' => $faqIndex,
                            'deleted_count' => $deletedCount,
                        ]);
                    }
                    continue;
                }

                // Skip if value not set (except for file types which will be handled below)
                if (!isset($contentData['value']) && !in_array($contentType, ['image', 'video'])) {
                    continue;
                }

                $mediaId = null;
                $value = $contentData['value'] ?? '';

                // Check if this content has an uploaded file
                if (in_array($contentType, ['image', 'video'])) {
                    // Extract field name from content key
                    // Format: "hero-images-1_image_1" -> extract "image_1"
                    $parts = explode('_', $contentData['key']);
                    $fieldName = implode('_', array_slice($parts, 1));

                    // Try different matching strategies
                    $matchingMedia = null;

                    // Try exact match first
                    if (isset($uploadedFiles[$fieldName])) {
                        $matchingMedia = $uploadedFiles[$fieldName];
                    }

                    // Try with full key
                    if (!$matchingMedia && isset($uploadedFiles[$contentData['key']])) {
                        $matchingMedia = $uploadedFiles[$contentData['key']];
                    }

                    // Try partial match
                    if (!$matchingMedia) {
                        foreach ($uploadedFiles as $uploadKey => $media) {
                            if (strpos($contentData['key'], $uploadKey) !== false ||
                                strpos($uploadKey, $fieldName) !== false) {
                                $matchingMedia = $media;
                                break;
                            }
                        }
                    }
                    
                    // Special handling for project image: project_XX_image -> project_XX_media_src
                    if (!$matchingMedia && preg_match('/project_(.+)_media_src$/', $contentData['key'], $matches)) {
                        $projectId = $matches[1];
                        $imageKey = 'project_' . $projectId . '_image';
                        if (isset($uploadedFiles[$imageKey])) {
                            $matchingMedia = $uploadedFiles[$imageKey];
                        }
                    }

                    if ($matchingMedia) {
                        // Collect old media for deletion after transaction completes
                        $existingContent = $section->contents()->where('key', $contentData['key'])->first();
                        if ($existingContent && $existingContent->media_id) {
                            $mediaToDelete[] = $existingContent->media_id;
                        }

                        $mediaId = $matchingMedia->id;
                        $value = 'storage/' . $matchingMedia->path;

                        \Illuminate\Support\Facades\Log::info('Media linked to content', [
                            'content_key' => $contentData['key'],
                            'media_id' => $mediaId,
                            'value' => $value,
                        ]);
                    } elseif ($value === 'pending_upload') {
                        // If value is pending_upload but no file found, skip this content
                        continue;
                    }
                }

                // Skip empty values (except for required fields like title, desc, image, link)
                $requiredSuffixes = ['_title', '_desc', '_image', '_link', '_name'];
                $isRequired = false;
                foreach ($requiredSuffixes as $suffix) {
                    if (str_ends_with($contentData['key'], $suffix)) {
                        $isRequired = true;
                        break;
                    }
                }
                
                // Skip if value is empty/falsy and not a required field
                // empty() catches: '', null, 0, '0', [], false
                if (!$isRequired && (empty($value) || $value === '0') && $mediaId === null) {
                    continue;
                }

                // Update or create content
                $content = $section->contents()->updateOrCreate(
                    ['key' => $contentData['key']],
                    [
                        'type' => $contentType,
                        'value' => $value,
                        'order' => $contentData['order'] ?? 0,
                        'media_id' => $mediaId,
                    ]
                );
                $updated[] = $content;
            }

            DB::commit();

            // Now delete old media files after transaction is committed
            // This prevents foreign key issues during the transaction
            foreach (array_unique($mediaToDelete) as $mediaId) {
                $this->deleteOldMedia($mediaId);
            }

            \Illuminate\Support\Facades\Log::info('BatchUpdate completed', [
                'updated_count' => count($updated),
                'media_deleted' => count(array_unique($mediaToDelete)),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contents updated successfully',
                'data' => $updated,
                'files_uploaded' => count($uploadedFiles),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('BatchUpdate error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan perubahan, coba lagi.'
            ], 500);
        }
    }

    /**
     * Get section content
     */
    public function getContent($sectionKey)
    {
        try {
            $section = Section::with('contents.media')->where('key', $sectionKey)->first();

            if (!$section) {
                return response()->json([
                    'success' => false,
                    'message' => 'Section not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $section
            ]);

        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat konten, coba lagi.'
            ], 500);
        }
    }

    /**
     * Upload media file
     */
    private function uploadMedia($file)
    {
        // Type and extension come from the file's content, never from the client.
        $type = str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'image';
        $filename = time() . '_' . uniqid() . '.' . $file->guessExtension();
        $path = $file->storeAs('media/' . $type . 's', $filename, 'public');

        return Media::create([
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'path' => $path,
            'type' => $type,
        ]);
    }

    /**
     * Delete media
     */
    public function deleteMedia($id)
    {
        try {
            $media = Media::findOrFail($id);

            // Delete file from storage
            if (Storage::disk('public')->exists($media->path)) {
                Storage::disk('public')->delete($media->path);
            }

            $media->delete();

            return response()->json([
                'success' => true,
                'message' => 'Media deleted successfully'
            ]);

        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus media, coba lagi.'
            ], 500);
        }
    }
}
