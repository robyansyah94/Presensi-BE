<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    protected $fillable = [
        'evaluator_id',
        'evaluatee_id',
        'assessment_date',
        'period',
        'period_label',
        'general_notes',
    ];

    protected $casts = [
        'assessment_date' => 'date',
    ];

    //Relasi
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function evaluatee(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'evaluatee_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(AssessmentDetail::class);
    }

    //Accessor average_score
    public function getAverageScoreAttribute(): float
    {
        // Gunakan relasi yang sudah di-load (hindari query tambahan)
        $details = $this->relationLoaded('details')
            ? $this->details
            : $this->details()->get();

        if ($details->isEmpty()) return 0;

        return round($details->avg('score'), 1);
    }
}
