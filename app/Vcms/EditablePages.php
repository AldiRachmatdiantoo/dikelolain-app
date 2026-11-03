<?php

namespace App\Vcms;

/**
 * The public pages an admin edits in place, for the dashboard's "Edit halaman" links.
 * Detail pages (a service, a team member) are edited from their list pages.
 */
final class EditablePages
{
    /**
     * @return list<array{label: string, path: string, icon: string}>
     */
    public static function all(): array
    {
        return [
            ['label' => 'Home', 'path' => '/', 'icon' => 'fa-house'],
            ['label' => 'About', 'path' => '/about', 'icon' => 'fa-info-circle'],
            ['label' => 'Services', 'path' => '/services', 'icon' => 'fa-briefcase'],
            ['label' => 'Portfolio', 'path' => '/portfolio', 'icon' => 'fa-folder-open'],
            ['label' => 'Team', 'path' => '/team', 'icon' => 'fa-users'],
            ['label' => 'Pricing', 'path' => '/pricing', 'icon' => 'fa-tag'],
            ['label' => 'Contact', 'path' => '/contact', 'icon' => 'fa-envelope'],
            ['label' => 'FAQs', 'path' => '/faqs', 'icon' => 'fa-circle-question'],
            ['label' => 'Testimonials', 'path' => '/testimonials', 'icon' => 'fa-star'],
        ];
    }
}
