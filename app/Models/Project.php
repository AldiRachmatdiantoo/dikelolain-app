<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /** media_type => label in the editor */
    public const MEDIA_TYPES = ['video' => 'Video', 'image' => 'Desain'];

    protected $fillable = ['title', 'description', 'media_type', 'video_url', 'image', 'link', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function isVideo(): bool
    {
        return $this->media_type === 'video';
    }

    /**
     * The video id of a YouTube link (watch, youtu.be, embed, shorts or live), or null.
     */
    public static function youtubeId(?string $url): ?string
    {
        $pattern = '~^https?://(?:www\.|m\.)?(?:youtube(?:-nocookie)?\.com/(?:watch\?(?:[^#]*&)?v=|embed/|shorts/|live/)|youtu\.be/)([A-Za-z0-9_-]{11})(?![A-Za-z0-9_-])~i';

        return preg_match($pattern, (string) $url, $matches) ? $matches[1] : null;
    }

    /** Privacy-enhanced embed URL of the video, or '' when there is none. */
    protected function embedUrl(): Attribute
    {
        return Attribute::get(function () {
            $id = self::youtubeId($this->video_url);

            return $id ? "https://www.youtube-nocookie.com/embed/{$id}" : '';
        });
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => Media::url($this->image));
    }

    /** What the project modal shows: the video embed or the image. */
    protected function mediaUrl(): Attribute
    {
        return Attribute::get(fn () => $this->isVideo() ? $this->embed_url : $this->image_url);
    }

    protected function categoryLabel(): Attribute
    {
        return Attribute::get(fn () => $this->isVideo() ? 'Video Production' : 'Design');
    }
}
