<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * A photo attached to a journal entry ("Image Moments"), stored on the public disk.
 */
class JournalEntryImage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'path',
    ];

    protected static function booted(): void
    {
        // Remove the file together with the record
        static::deleted(fn (JournalEntryImage $image) => Storage::disk('public')->delete($image->path));
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    /**
     * Public URL; asset() follows the current host, so it works on 127.0.0.1:8000 as well as APP_URL.
     */
    public function url(): string
    {
        return asset('storage/'.$this->path);
    }
}
