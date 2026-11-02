<?php

namespace App\Services\Site;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MinecraftServerStatus
{
    /**
     * @return array{available: bool, online: bool, players: int|null, max_players: int|null}
     */
    public function get(): array
    {
        return Cache::remember('site.minecraft-status.v1', now()->addMinute(), function (): array {
            try {
                $response = Http::timeout(3)->get('https://api.mcsrvstat.us/3/play.vunex.nl');
            } catch (ConnectionException $exception) {
                Log::warning('Vunex server status could not be reached.', ['exception' => $exception->getMessage()]);

                return ['available' => false, 'online' => false, 'players' => null, 'max_players' => null];
            }

            if (! $response->successful()) {
                Log::warning('Vunex server status returned an unsuccessful response.', ['status' => $response->status()]);

                return ['available' => false, 'online' => false, 'players' => null, 'max_players' => null];
            }

            $data = $response->json();
            if (! is_array($data)) {
                Log::warning('Vunex server status returned an invalid response.');

                return ['available' => false, 'online' => false, 'players' => null, 'max_players' => null];
            }

            $online = ($data['online'] ?? false) === true;
            $playerData = is_array($data['players'] ?? null) ? $data['players'] : [];
            $players = $playerData['online'] ?? null;
            $maxPlayers = $playerData['max'] ?? null;

            return [
                'available' => true,
                'online' => $online,
                'players' => $online && is_numeric($players) ? (int) $players : ($online ? 0 : null),
                'max_players' => $online && is_numeric($maxPlayers) ? (int) $maxPlayers : null,
            ];
        });
    }
}
