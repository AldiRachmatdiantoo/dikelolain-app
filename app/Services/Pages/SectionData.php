<?php

namespace App\Services\Pages;

use App\Models\Section;
use App\Services\AchievementService;
use App\Services\ClientLogoService;
use App\Services\CounterService;
use App\Services\SettingService;
use App\Services\TeamMemberService;
use App\Services\ToolService;
use App\Vcms\Fields\AchievementFields;
use App\Vcms\Fields\ClientLogoFields;
use App\Vcms\Fields\CounterFields;
use App\Vcms\Fields\TeamMemberFields;
use App\Vcms\Fields\ToolFields;
use Illuminate\Support\Collection;

/**
 * View data for sections that appear on several pages, so each page service
 * only lists the sections it shows.
 */
class SectionData
{
    public function __construct(
        private SettingService $settings,
        private ClientLogoService $clientLogos,
        private ToolService $tools,
        private AchievementService $achievements,
        private CounterService $counters,
        private TeamMemberService $teamMembers,
    ) {
    }

    /**
     * Settings service for titles, texts and links ($settings->get('key')).
     */
    public function settings(): array
    {
        return ['settings' => $this->settings];
    }

    /**
     * Old key-value content for the sections not migrated yet (removed in Epic 4).
     *
     * @return array{sections: Collection}
     */
    public function legacy(): array
    {
        return ['sections' => Section::with('contents.media')->active()->ordered()->get()->keyBy('key')];
    }

    /**
     * For partials/client-logos.blade.php.
     */
    public function clientLogos(bool $editing): array
    {
        return [
            'clientLogos' => $this->clientLogos->all(activeOnly: !$editing),
            'clientLogoFields' => $editing ? ClientLogoFields::definition() : [],
        ];
    }

    /**
     * For partials/creative-tools.blade.php.
     */
    public function tools(bool $editing): array
    {
        return [
            'tools' => $this->tools->all(activeOnly: !$editing),
            'toolFields' => $editing ? ToolFields::definition() : [],
        ];
    }

    /**
     * For partials/achievements.blade.php.
     */
    public function achievements(bool $editing): array
    {
        return [
            'achievements' => $this->achievements->all(activeOnly: !$editing),
            'achievementFields' => $editing ? AchievementFields::definition() : [],
        ];
    }

    /**
     * For partials/cta.blade.php.
     */
    public function counters(bool $editing): array
    {
        return [
            'counters' => $this->counters->all(activeOnly: !$editing),
            'counterFields' => $editing ? CounterFields::definition() : [],
        ];
    }

    /**
     * For partials/team.blade.php: the first four active members (all of them are on /team).
     */
    public function team(bool $editing): array
    {
        return [
            'teamMembers' => $this->teamMembers->highlights(4),
            'teamMemberFields' => $editing ? TeamMemberFields::definition() : [],
        ];
    }
}
