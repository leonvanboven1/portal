<?php

namespace App\Services\Plugin;

use App\Helpers\MinecraftHelper;
use Carbon\Carbon;

/**
 * The portal decides each VOG from the player's recent criminal records. The plugin
 * stores the issued VOG and gives the officer the book in-game.
 */
class VogService
{
    /** Why the plugin refused, by the code it answers with. */
    private const ERRORS = [
        'officer_offline' => 'Je moet online zijn op de server: het VOG-boek komt in je inventory.',
        'no_permission' => 'Je mag in-game geen VOG afgeven (openminetopia.books.vog).',
        'book_disabled' => 'Het VOG-boek staat uit in books.yml van de plugin.',
        'player_not_found' => 'Deze speler is nog nooit op de server geweest.',
        'vog_manual' => 'Op deze server vullen agenten VOG\'s in-game met de hand in, met /boek krijg vog.',
    ];

    public function __construct(protected PluginApiService $apiService) {}

    /**
     * The player's VOGs, newest first, and whether the plugin is configured to issue one;
     * null when the plugin gave no answer.
     *
     * @return array{automatic: bool, vogs: list<array{positive: bool, record_count: int, officer_name: ?string, date: Carbon, valid_until: Carbon, valid: bool}>}|null
     */
    public function forPlayer(string $uuid): ?array
    {
        $data = $this->apiService->get("/api/player/{$uuid}/vogs");

        if (! is_array($data) || ! isset($data['vogs'])) {
            return null;
        }

        return [
            'automatic' => (bool) ($data['automatic'] ?? true),
            'vogs' => array_map(fn (array $vog) => $this->present($vog), array_values($data['vogs'])),
        ];
    }

    /** @param list<array<string, mixed>> $records
     *  @return list<array<string, mixed>>
     */
    public function recentRecords(array $records): array
    {
        $cutoff = now()->subMonth();

        return array_values(array_filter(
            $records,
            fn (array $record) => $this->recordDate($record)->greaterThanOrEqualTo($cutoff),
        ));
    }

    public function recordDate(array $record): Carbon
    {
        $date = $record['date'] ?? throw new \UnexpectedValueException('A criminal record is missing its date.');

        if (is_numeric($date)) {
            $timestamp = (int) $date;

            return abs($timestamp) >= 100_000_000_000
                ? Carbon::createFromTimestampMs($timestamp)
                : Carbon::createFromTimestamp($timestamp);
        }

        return Carbon::parse($date);
    }

    /**
     * Asks the plugin to issue the VOG with the outcome already decided by the portal.
     *
     * @return array{vog: array}|array{error: string}
     */
    public function issue(string $playerUuid, string $officerUuid, int $recordCount): array
    {
        $positive = $recordCount === 0;
        $data = $this->apiService->postOrError("/api/player/{$playerUuid}/vogs", [
            'officer' => $officerUuid,
            'positive' => $positive,
            'record_count' => $recordCount,
        ]);

        if (($data['success'] ?? false) && isset($data['vog'])) {
            return ['vog' => $this->present(array_merge($data['vog'], [
                'positive' => $positive,
                'record_count' => $recordCount,
            ]))];
        }

        return ['error' => self::ERRORS[$data['code'] ?? null] ?? 'De plugin gaf geen VOG af. Is hij bereikbaar en bijgewerkt?'];
    }

    private function present(array $vog): array
    {
        $validUntil = Carbon::createFromTimestampMs($vog['valid_until']);

        return [
            'positive' => (bool) $vog['positive'],
            'record_count' => (int) ($vog['record_count'] ?? 0),
            'officer_name' => isset($vog['officer']) ? MinecraftHelper::getName($vog['officer']) : null,
            'date' => Carbon::createFromTimestampMs($vog['date']),
            'valid_until' => $validUntil,
            'valid' => $validUntil->isFuture(),
        ];
    }
}
