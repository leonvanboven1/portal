<?php

namespace App\Http\Controllers\Portal\Police;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Plugin\VogService;
use Illuminate\Http\Request;

class VogController extends Controller
{
    public function index(Request $request, VogService $vogs)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:80'],
            'player' => ['nullable', 'integer', 'exists:users,id'],
        ]);
        $search = trim($validated['search'] ?? '');

        $players = User::query()
            ->whereNotNull('minecraft_plain_uuid')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('minecraft_username', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('minecraft_username')
            ->paginate(10)
            ->withQueryString();

        $selectedPlayer = null;
        $recentRecords = [];
        $playerVogs = null;

        if (isset($validated['player'])) {
            $selectedPlayer = User::query()
                ->whereNotNull('minecraft_plain_uuid')
                ->findOrFail($validated['player']);
            $recentRecords = $vogs->recentRecords($selectedPlayer->criminal_records);
            $playerVogs = $vogs->forPlayer($selectedPlayer->minecraft_plain_uuid);
        }

        return view('portal.police.vogs.index', compact(
            'players',
            'search',
            'selectedPlayer',
            'recentRecords',
            'playerVogs',
            'vogs',
        ));
    }
}
