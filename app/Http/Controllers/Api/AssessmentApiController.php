<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentCategory;
use App\Models\AssessmentDetail;
use Illuminate\Http\Request;

class AssessmentApiController extends Controller
{
    // Ambil riwayat semua penilaian milik karyawan yang login
    // GET /api/assessment/riwayat
    public function riwayat(Request $request)
    {
        $karyawan = $request->user()->karyawan;

        if (!$karyawan) {
            return response()->json(['message' => 'Data karyawan tidak ditemukan.'], 404);
        }

        $assessments = Assessment::with(['details.category', 'evaluator'])
            ->where('evaluatee_id', $karyawan->id)
            ->orderByDesc('assessment_date')
            ->get()
            ->map(function ($a) {
                return [
                    'id'              => $a->id,
                    'assessment_date' => $a->assessment_date->format('d M Y'),
                    'period'          => $a->period,
                    'period_label'    => $a->period_label,
                    'general_notes'   => $a->general_notes,
                    'average_score'   => $a->average_score,
                    'evaluator_name'  => $a->evaluator->name,
                    'details'         => $a->details->map(fn($d) => [
                        'category' => $d->category->name,
                        'score'    => $d->score,
                    ]),
                ];
            });

        return response()->json([
            'data' => $assessments,
        ]);
    }

    // Data radar chart — rata-rata per kategori (semua waktu)
    //  GET /api/assessment/radar
    public function radar(Request $request)
    {
        $karyawan = $request->user()->karyawan;

        if (!$karyawan) {
            return response()->json(['message' => 'Data karyawan tidak ditemukan.'], 404);
        }

        $categories = AssessmentCategory::active()->orderBy('name')->get();

        $radarData = $categories->map(function ($cat) use ($karyawan) {
            $avg = AssessmentDetail::whereHas('assessment', fn($q) => $q->where('evaluatee_id', $karyawan->id))
                ->where('category_id', $cat->id)
                ->avg('score');

            return [
                'category' => $cat->name,
                'average'  => round($avg ?? 0, 1),
            ];
        });

        return response()->json([
            'data' => $radarData,
        ]);
    }

    
    // Detail satu penilaian
    // GET /api/assessment/{id}
    public function show(Request $request, Assessment $assessment)
    {
        $karyawan = $request->user()->karyawan;

        // Pastikan karyawan hanya bisa lihat penilaian miliknya sendiri
        if (!$karyawan || $assessment->evaluatee_id !== $karyawan->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        $assessment->load(['details.category', 'evaluator']);

        return response()->json([
            'data' => [
                'id'              => $assessment->id,
                'assessment_date' => $assessment->assessment_date->format('d M Y'),
                'period'          => $assessment->period,
                'period_label'    => $assessment->period_label,
                'general_notes'   => $assessment->general_notes,
                'average_score'   => $assessment->average_score,
                'evaluator_name'  => $assessment->evaluator->name,
                'details'         => $assessment->details->map(fn($d) => [
                    'category'    => $d->category->name,
                    'description' => $d->category->description,
                    'score'       => $d->score,
                ]),
            ],
        ]);
    }
}