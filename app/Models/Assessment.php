<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $table = 'assessments';

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

    /**
     * Admin yang menilai
     */
    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    /**
     * Karyawan yang dinilai
     */
    public function evaluatee()
    {
        return $this->belongsTo(Karyawan::class, 'evaluatee_id');
    }

    /**
     * Detail nilai per kategori
     */
    public function details()
    {
        return $this->hasMany(AssessmentDetail::class, 'assessment_id');
    }

    /**
     * Rata-rata nilai semua kategori
     */
    public function getAverageScoreAttribute(): float
    {
        $avg = $this->details()->avg('score');
        return round($avg ?? 0, 1);
    }
}
