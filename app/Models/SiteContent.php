<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteContent extends Model
{
    protected $fillable = [
        'hero_title',
        'hero_text',
        'about_title',
        'about_text',
        'rules_title',
        'rules_text',
    ];

    public static function current(): self
    {
        return self::firstOrCreate(['id' => 1], [
            'hero_title' => 'Jouw verhaal begint hier.',
            'hero_text' => 'Ontdek de stad, bouw aan je toekomst en beleef samen een wereld vol mogelijkheden.',
            'about_title' => 'Meer dan een Minecraft-server.',
            'about_text' => 'Een levendige roleplaystad waar jouw keuzes het verschil maken. Ontmoet anderen, ontdek wat bij je past en bouw mee aan het verhaal van onze stad.',
            'rules_title' => 'Samen houden we de stad leuk.',
            'rules_text' => "Behandel elkaar met respect.\nHoud roleplay eerlijk en speel mee met het verhaal.\nLuister naar de aanwijzingen van het team.",
        ]);
    }
}
