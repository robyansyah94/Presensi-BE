<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentCategory;
use App\Models\AssessmentDetail;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    /**
     * Dashboard penilaian — daftar karyawan + progress penilai
     */
    public function index(Request $request)
    {
        $period      = $request->get('period', 'bulanan');
        $periodLabel = $this->buildPeriodLabel($period);

        // Semua karyawan aktif
        $semuaKaryawan = Karyawan::with(['user', 'jabatan'])
            ->where('status', 'aktif')
            ->join('users', 'users.id', '=', 'karyawan.users_id')
            ->orderByRaw('LOWER(users.name) ASC')
            ->select('karyawan.*')
            ->get();

        // Karyawan yang sudah dinilai pada periode ini
        $sudahDinilaiIds = Assessment::where('period', $period)
            ->where('period_label', $periodLabel)
            ->pluck('evaluatee_id')
            ->toArray();

        $totalKaryawan  = $semuaKaryawan->count();
        $totalDinilai   = count($sudahDinilaiIds);

        // Tandai status sudah/belum dinilai
        $karyawanList = $semuaKaryawan->map(function ($k) use ($sudahDinilaiIds, $period, $periodLabel) {
            $k->sudah_dinilai = in_array($k->id, $sudahDinilaiIds);
            $k->assessment    = $k->sudah_dinilai
                ? Assessment::where('evaluatee_id', $k->id)
                ->where('period', $period)
                ->where('period_label', $periodLabel)
                ->first()
                : null;
            return $k;
        });

        return view('admin.assessment.index', compact(
            'karyawanList',
            'totalKaryawan',
            'totalDinilai',
            'period',
            'periodLabel',
        ));
    }

    /**
     * Form input penilaian untuk satu karyawan
     */
    public function create(Request $request, Karyawan $karyawan)
    {
        $period      = $request->get('period', 'bulanan');
        $periodLabel = $this->buildPeriodLabel($period);

        // Cek apakah sudah pernah dinilai periode ini
        $existing = Assessment::where('evaluatee_id', $karyawan->id)
            ->where('period', $period)
            ->where('period_label', $periodLabel)
            ->first();

        if ($existing) {
            return redirect()->route('admin.assessment.edit', [$existing->id, 'period' => $period]);
        }

        $categories = AssessmentCategory::active()->orderBy('name')->get();

        return view('admin.assessment.form', compact(
            'karyawan',
            'categories',
            'period',
            'periodLabel',
        ));
    }

    /**
     * Simpan penilaian baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'evaluatee_id'  => 'required|exists:karyawan,id',
            'period'        => 'required|in:harian,mingguan,bulanan',
            'period_label'  => 'required|string',
            'general_notes' => 'nullable|string|max:1000',
            'scores'        => 'required|array|min:1',
            'scores.*'      => 'required|integer|min:1|max:5',
        ], [
            'scores.required'   => 'Minimal ada satu kategori yang dinilai.',
            'scores.*.min'      => 'Nilai minimal adalah 1.',
            'scores.*.max'      => 'Nilai maksimal adalah 5.',
        ]);

        // Pastikan belum ada penilaian duplikat
        $exists = Assessment::where('evaluatee_id', $request->evaluatee_id)
            ->where('period', $request->period)
            ->where('period_label', $request->period_label)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Karyawan ini sudah dinilai untuk periode yang sama.');
        }

        $evaluatorId = auth()->id();

        DB::transaction(function () use ($request, $evaluatorId) {
            $assessment = Assessment::create([
                'evaluator_id'    => $evaluatorId,
                'evaluatee_id'    => $request->evaluatee_id,
                'assessment_date' => now()->toDateString(),
                'period'          => $request->period,
                'period_label'    => $request->period_label,
                'general_notes'   => $request->general_notes,
            ]);

            foreach ($request->scores as $categoryId => $score) {
                AssessmentDetail::create([
                    'assessment_id' => $assessment->id,
                    'category_id'   => $categoryId,
                    'score'         => $score,
                ]);
            }
        });

        // Cek apakah ada karyawan berikutnya yang belum dinilai
        $nextKaryawan = $this->getNextUnnilaiKaryawan(
            $request->evaluatee_id,
            $request->period,
            $request->period_label
        );

        if ($nextKaryawan) {
            return redirect()
                ->route('admin.assessment.create', [$nextKaryawan->id, 'period' => $request->period])
                ->with('success', 'Penilaian berhasil disimpan. Lanjut ke karyawan berikutnya!');
        }

        return redirect()->route('admin.assessment.index', ['period' => $request->period])
            ->with('success', 'Penilaian berhasil disimpan. Semua karyawan sudah dinilai! 🎉');
    }

    /**
     * Form edit penilaian yang sudah ada
     */
    public function edit(Assessment $assessment)
    {
        $karyawan   = $assessment->evaluatee->load(['user', 'jabatan']);
        $categories = AssessmentCategory::active()->orderBy('name')->get();
        $scores     = $assessment->details->pluck('score', 'category_id');

        return view('admin.assessment.form', compact(
            'assessment',
            'karyawan',
            'categories',
            'scores',
        ));
    }

    /**
     * Update penilaian
     */
    public function update(Request $request, Assessment $assessment)
    {
        $request->validate([
            'general_notes' => 'nullable|string|max:1000',
            'scores'        => 'required|array|min:1',
            'scores.*'      => 'required|integer|min:1|max:5',
        ]);

        DB::transaction(function () use ($request, $assessment) {
            $assessment->update([
                'general_notes'   => $request->general_notes,
                'assessment_date' => now()->toDateString(),
            ]);

            // Hapus detail lama, simpan yang baru
            $assessment->details()->delete();

            foreach ($request->scores as $categoryId => $score) {
                AssessmentDetail::create([
                    'assessment_id' => $assessment->id,
                    'category_id'   => $categoryId,
                    'score'         => $score,
                ]);
            }
        });

        return redirect()->route('admin.assessment.index', ['period' => $assessment->period])
            ->with('success', 'Penilaian berhasil diperbarui.');
    }

    /**
     * Hapus penilaian
     */
    public function destroy(Assessment $assessment)
    {
        $period = $assessment->period;
        $assessment->delete(); // cascade ke details

        return redirect()->route('admin.assessment.index', ['period' => $period])
            ->with('success', 'Penilaian berhasil dihapus.');
    }

    /**
     * Halaman laporan / rapor per karyawan
     */
    public function report(Karyawan $karyawan)
    {
        $karyawan->load(['user', 'jabatan']);

        // Ambil semua riwayat penilaian, urutkan dari terbaru
        $assessments = Assessment::with(['details.category'])
            ->where('evaluatee_id', $karyawan->id)
            ->orderByDesc('assessment_date')
            ->get();

        // Data untuk radar chart — rata-rata per kategori dari semua penilaian
        $categories  = AssessmentCategory::active()->orderBy('name')->get();
        $radarData   = $categories->map(function ($cat) use ($karyawan) {
            $avg = AssessmentDetail::whereHas('assessment', fn($q) => $q->where('evaluatee_id', $karyawan->id))
                ->where('category_id', $cat->id)
                ->avg('score');
            return [
                'category' => $cat->name,
                'average'  => round($avg ?? 0, 1),
            ];
        });

        return view('admin.assessment.report', compact(
            'karyawan',
            'assessments',
            'radarData',
        ));
    }

    // ── Helper ──────────────────────────────────────────────────

    /**
     * Buat label periode otomatis berdasarkan tipe
     */
    private function buildPeriodLabel(string $period): string
    {
        return match ($period) {
            'harian'   => now()->translatedFormat('d F Y'),
            'mingguan' => 'Minggu ' . now()->weekOfMonth . ' ' . now()->translatedFormat('F Y'),
            default    => now()->translatedFormat('F Y'),
        };
    }

    /**
     * Ambil karyawan aktif berikutnya yang belum dinilai
     */
    private function getNextUnnilaiKaryawan(int $currentId, string $period, string $periodLabel): ?Karyawan
    {
        $sudahDinilaiIds = Assessment::where('period', $period)
            ->where('period_label', $periodLabel)
            ->pluck('evaluatee_id')
            ->toArray();

        return Karyawan::with('user')
            ->where('karyawan.status', 'aktif')
            ->whereNotIn('karyawan.id', $sudahDinilaiIds)
            ->where('karyawan.id', '!=', $currentId)
            ->join('users', 'users.id', '=', 'karyawan.users_id')
            ->orderByRaw('LOWER(users.name) ASC')
            ->select('karyawan.*')
            ->first();
    }
}
