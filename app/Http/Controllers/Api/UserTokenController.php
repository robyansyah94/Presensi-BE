<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserToken;
use Illuminate\Http\Request;

class UserTokenController extends Controller
{
    /**
     * GET /api/tokens
     * Inventory token milik user.
     */
    public function index(Request $request)
    {
        $tokens = UserToken::where('user_id', $request->user()->id)
            ->with(['item', 'attendance'])
            ->latest()
            ->get()
            ->map(function ($token) {
                return [
                    'id'          => $token->id,
                    'status'      => $token->status,
                    'status_label'=> $token->status_label,
                    'is_available'=> $token->isAvailable(),
                    'expires_at'  => $token->expires_at?->format('d M Y'),
                    'created_at'  => $token->created_at->format('d M Y'),
                    'used_at'     => $token->attendance
                                        ? $token->attendance->tanggal
                                        : null,
                    'item' => [
                        'id'                => $token->item->id,
                        'item_name'         => $token->item->item_name,
                        'icon'              => $token->item->icon,
                        'token_type'        => $token->item->token_type,
                        'token_type_label'  => $token->item->token_type_label,
                        'tolerance_minutes' => $token->item->tolerance_minutes,
                        'description'       => $token->item->description,
                    ],
                ];
            });

        return response()->json([
            'tokens'    => $tokens,
            'available' => $tokens->where('is_available', true)->count(),
            'total'     => $tokens->count(),
        ]);
    }
}