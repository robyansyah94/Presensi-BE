<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentCategory extends Model
{
    protected $table = 'assessment_categories';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    //Relasi ke detail penilaian
    public function details()
    {
        return $this->hasMany(AssessmentDetail::class, 'category_id');
    }

    // Scope hanya kategori yang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
