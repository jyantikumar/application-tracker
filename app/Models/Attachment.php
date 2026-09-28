<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;

    public const TYPE_COVER_LETTER = 'cover_letter';
    public const TYPE_PORTFOLIO = 'portfolio';
    public const TYPE_CERT = 'cert';
    public const TYPE_OTHER = 'other';

    public const TYPE_LABELS = [
        self::TYPE_COVER_LETTER => 'Cover Letter',
        self::TYPE_PORTFOLIO => 'Portfolio',
        self::TYPE_CERT => 'Certificate',
        self::TYPE_OTHER => 'Other',
    ];

    protected $fillable = [
        'application_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    protected $appends = [
        'file_type_label',
        'download_url',
        'formatted_size',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'application_id');
    }

    public function getFileTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->file_type] ?? 'Other';
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('attachments.download', $this);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;

        if ($bytes < 1024) {
            return $bytes.' B';
        }

        if ($bytes < 1024 * 1024) {
            return round($bytes / 1024, 1).' KB';
        }

        return round($bytes / (1024 * 1024), 1).' MB';
    }

    public function deleteFile(): void
    {
        if ($this->file_path) {
            Storage::disk('local')->delete($this->file_path);
        }
    }

    protected static function booted(): void
    {
        static::deleting(function (Attachment $attachment) {
            $attachment->deleteFile();
        });
    }
}
