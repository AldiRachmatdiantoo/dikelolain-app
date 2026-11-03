<?php

namespace App\Services;

/**
 * The public URLs search engines should index: the main pages plus each active
 * service and team member detail page.
 */
class SitemapService
{
    /**
     * Main pages by route name (the horizontal slider is an alternative view of /portfolio).
     */
    private const PAGES = ['home', 'about', 'services', 'portfolio', 'pricing', 'faqs', 'team', 'testimonials', 'contact'];

    public function __construct(
        private ServiceService $services,
        private TeamMemberService $teamMembers,
    ) {
    }

    /**
     * @return list<array{loc: string, lastmod: ?string}>
     */
    public function urls(): array
    {
        $urls = array_map(fn (string $name) => ['loc' => route($name), 'lastmod' => null], self::PAGES);

        foreach ($this->services->all(activeOnly: true) as $service) {
            $urls[] = ['loc' => route('pages.service.single', $service->slug), 'lastmod' => $service->updated_at?->toAtomString()];
        }

        foreach ($this->teamMembers->all(activeOnly: true) as $member) {
            $urls[] = ['loc' => route('pages.team.single', $member->slug), 'lastmod' => $member->updated_at?->toAtomString()];
        }

        return $urls;
    }
}
