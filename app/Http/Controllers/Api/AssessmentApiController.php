<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssessmentApiController extends Controller
{
    /**
     * Riwayat penilaian milik karyawan yang login
     */
    public function riwayat(Request $request)
    {
        $user     = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return response()->json(['data' => []]);
        }

        $query = Assessment::with(['details.category', 'evaluator'])
            ->where('evaluatee_id', $karyawan->id)
            ->orderByDesc('assessment_date');

        if ($request->filled('period')) {
            $query->where('period', $request->period);
        }

        $assessments = $query->get()->map(function ($a) {
            return [
                'id'              => $a->id,
                'period'          => $a->period,
                'period_label'    => $a->period_label,
                'assessment_date' => $a->assessment_date,
                'average_score'   => $a->average_score,
                'general_notes'   => $a->general_notes,
                'evaluator'       => optional($a->evaluator)->name,
                'details'         => $a->details->map(fn($d) => [
                    'id'       => $d->id,
                    'score'    => $d->score,
                    'category' => $d->category ? ['name' => $d->category->name] : null,
                ]),
            ];
        });

        return response()->json(['data' => $assessments]);
    }

    /**
     * Data radar chart — rata-rata per kategori
     */
    public function radar(Request $request)
    {
        $user     = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return response()->json(['data' => []]);
        }

        $query = Assessment::with('details.category')
            ->where('evaluatee_id', $karyawan->id);

        if ($request->filled('period')) {
            $query->where('period', $request->period);
        }

        $assessments = $query->get();

        // Kumpulkan semua score per kategori
        $categoryScores = [];
        foreach ($assessments as $assessment) {
            foreach ($assessment->details as $detail) {
                if (!$detail->category) continue;
                $name = $detail->category->name;
                $categoryScores[$name][] = $detail->score;
            }
        }

        // Hitung rata-rata per kategori
        $radarData = collect($categoryScores)->map(function ($scores, $category) {
            return [
                'category' => $category,
                'average'  => round(array_sum($scores) / count($scores), 1),
            ];
        })->values();

        return response()->json(['data' => $radarData]);
    }
}
