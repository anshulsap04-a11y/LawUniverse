<?php

namespace App\Services;

use App\Models\Resource;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ResourceService
{
    /**
     * Store an uploaded resource file & thumbnail.
     */
    public function storeResource(array $data, ?UploadedFile $file = null, ?UploadedFile $thumbnail = null, ?User $uploader = null): Resource
    {
        $filePath = null;
        $fileSize = null;
        $fileType = null;

        if ($file) {
            $fileName = Str::slug($data['title']) . '-' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('resources/files', $fileName, 'public');
            $fileSize = $file->getSize();
            $fileType = $file->getClientOriginalExtension();
        }

        $thumbnailPath = null;
        if ($thumbnail) {
            $thumbName = 'thumb-' . Str::slug($data['title']) . '-' . time() . '.' . $thumbnail->getClientOriginalExtension();
            $thumbnailPath = $thumbnail->storeAs('resources/thumbnails', $thumbName, 'public');
        }

        return Resource::create([
            'category_id' => $data['category_id'] ?? null,
            'subcategory_id' => $data['subcategory_id'] ?? null,
            'uploaded_by' => $uploader ? $uploader->id : auth()->id(),
            'title' => $data['title'],
            'slug' => Str::slug($data['title']) . '-' . Str::random(5),
            'description' => $data['description'] ?? null,
            'resource_type' => $data['resource_type'] ?? 'general',
            'file_path' => $filePath,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'thumbnail' => $thumbnailPath,
            'price' => isset($data['price']) ? (float)$data['price'] : 0.00,
            'is_free' => !empty($data['is_free']),
            'is_copyright' => !empty($data['is_copyright']),
            'reference_link' => $data['reference_link'] ?? null,
            'author' => $data['author'] ?? null,
            'year' => $data['year'] ?? date('Y'),
            'language' => $data['language'] ?? 'English',
            'status' => $data['status'] ?? 'published',
        ]);
    }

    /**
     * Check if a given user has permission to download or access a resource.
     */
    public function canAccess(User $user, Resource $resource): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($resource->is_free || $resource->price <= 0) {
            return true;
        }

        $activeSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('expiry_date', '>', now())
            ->first();

        if ($activeSubscription) {
            return true;
        }

        return $user->transactions()
            ->where('status', 'completed')
            ->where('package_id', $resource->id)
            ->exists();
    }
}
