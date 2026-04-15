<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FlexibilityItem;
use App\Models\UserToken;
use App\Services\PointLedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketplaceController extends Controller
{
    public function __construct(
        private readonly PointLedgerService $ledger
    ) {}

    /**
     * GET /api/marketplace
     * Daftar item beserta info saldo & stock user.
     */
    public function index(Request $request)
    {
        $user    = $request->user();
        $balance = $this->ledger->getBalance($user->id);
        $items   = FlexibilityItem::active()->get();

        $data = $items->map(function ($item) use ($user, $balance) {
            $purchasedThisMonth = $item->countPurchasedThisMonth($user->id);
            $canAfford          = $balance >= $item->point_cost;
            $stockOk            = $item->isStockAvailableFor($user->id);

            return [
                'id'                  => $item->id,
                'item_name'           => $item->item_name,
                'description'         => $item->description,
                'icon'                => $item->icon,
                'token_type'          => $item->token_type,
                'token_type_label'    => $item->token_type_label,
                'tolerance_minutes'   => $item->tolerance_minutes,
                'point_cost'          => $item->point_cost,
                'stock_limit'         => $item->stock_limit,
                'purchased_this_month'=> $purchasedThisMonth,
                'can_afford'          => $canAfford,
                'stock_available'     => $stockOk,
                'can_redeem'          => $canAfford && $stockOk,
            ];
        });

        return response()->json([
            'balance' => $balance,
            'items'   => $data,
        ]);
    }

    /**
     * POST /api/marketplace/{item}/redeem
     * Tukar poin dengan token kelonggaran.
     */
    public function redeem(Request $request, FlexibilityItem $item)
    {
        $user = $request->user();

        if (!$item->is_active) {
            return response()->json(['message' => 'Item tidak tersedia.'], 400);
        }

        $balance = $this->ledger->getBalance($user->id);

        if ($balance < $item->point_cost) {
            return response()->json([
                'message' => "Poin tidak mencukupi. Saldo Anda: {$balance} poin, dibutuhkan: {$item->point_cost} poin.",
            ], 400);
        }

        if (!$item->isStockAvailableFor($user->id)) {
            return response()->json([
                'message' => "Batas pembelian item ini bulan ini sudah tercapai ({$item->stock_limit}x/bulan).",
            ], 400);
        }

        // ── Atomic: kurangi poin + buat token ────────────────────────────────
        DB::transaction(function () use ($user, $item) {
            // Kurangi poin (SPEND)
            $this->ledger->record(
                userId:      $user->id,
                type:        'SPEND',
                amount:      $item->point_cost,
                description: "Penukaran: {$item->item_name}",
                refType:     'token_redemption',
                refId:       $item->id,
            );

            // Buat token di inventory user
            UserToken::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'status'  => 'AVAILABLE',
            ]);
        });

        return response()->json([
            'message'     => "Berhasil menukar {$item->point_cost} poin dengan \"{$item->item_name}\"! 🎫",
            'new_balance' => $this->ledger->getBalance($user->id),
        ]);
    }
}