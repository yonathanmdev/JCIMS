<?php

namespace App\Config;

class MenuConfig
{
    public static function getTree(): array
    {
        return [

            [
                'label' => 'Dashboard',
                'icon'  => 'fas fa-tachometer-alt',
                'url'   => '/dashboard',
                'roles' => ['system_admin', 'org_admin', 'team_leader', 'officer'],
            ],

            [
                'label' => 'መመዝገብ',
                'icon'  => 'fas fa-edit',
                'roles' => ['system_admin', 'org_admin'],
                'children' => [

                    [
                        'label' => 'register-developer',
                        'icon'  => 'fas fa-th',
                        'url'   => '/register-developer',
                        'badge' => 'New',
                        'roles' => ['system_admin']
                    ],

                    [
                        'label' => 'ቢሮ',
                        'url'   => '/register-organization',
                        'roles' => ['system_admin']
                    ],

                    [
                        'label' => 'ዘርፍ',
                        'url'   => '/sector-registration',
                        'roles' => ['system_admin']
                    ],

                     [
                        'label' => 'ንዑስ ዘርፍ',
                        'url'   => '/sub-sector-registration',
                        'roles' => ['system_admin']
                    ],


                    [
                        'label' => 'ቅርንጫፍ',
                        'url'   => '/register-branch',
                        'roles' => ['org_admin']
                    ],

                    [
                        'label' => 'ተቆጣጣሪ',
                        'url'   => '/register-user',
                        'roles' => ['system_admin', 'org_admin']
                    ],
                ]
            ],


            [
                'label' => 'ስራ ፈላጊ',
                'icon'  => 'fas fa-users',
                'roles' => ['team_leader', 'officer'],
                'children' => [
                    [
                        'label' => 'ስራ ፈላጊ መመዝገብ',
                        'url'   => '/jobseeker-registration',
                        'roles' => ['officer'],
                        'levels' => [3, 4]
                    ],
                    [
                        'label' => 'ዝርዝር',
                        'url'   => '/jobseekers-list',
                        'roles' => ['team_leader', 'officer']
                    ]
                ]
            ],

            
            
           
            [
                'label' => 'የተሰረዙ',
                'icon'  => 'fas fa-trash-restore',
                'class' => 'text-warning',
                'roles' => ['system_admin', 'org_admin', 'team_leader', 'officer'],
                'children' => [

                    [
                        'label' => 'ድርጅቶች',
                        'url'   => '/organization-deleted-lists',
                        'roles' => ['system_admin']
                    ],

                    [
                        'label' => 'ቅርንጫፎች',
                        'url'   => '/deleted-branches',
                        'roles' => ['org_admin']
                    ],

                    [
                        'label' => 'ተቆጣጣሪዎች',
                        'url'   => '/deleted-users',
                        'roles' => ['system_admin', 'org_admin']
                    ],

                    [
                        'label' => 'ቡድን መሪ',
                        'url'   => '/deleted-directors',
                        'roles' => ['team_leader', 'officer']
                    ],

                    [
                        'label' => 'መደብ',
                        'url'   => '/deleted-positions',
                        'roles' => ['team_leader', 'officer']
                    ],
                ]
            ],

            [
                'label' => 'Archived',
                'icon'  => 'fas fa-archive',
                'class' => 'text-info',
                'roles' => ['system_admin'],
                'children' => [
                    [
                        'label' => 'Organizations',
                        'url'   => '/archived-organizations',
                        'roles' => ['system_admin']
                    ],
                    [
                        'label' => 'Branches',
                        'url'   => '/archived-branches',
                        'roles' => ['system_admin']
                    ]
                ]
            ],

        ];
    }

    public static function getMenuForRoleAndLevel(string $role, int $level): array
    {
        $filtered = [];

        foreach (self::getTree() as $item) {

            if (!self::matches($item, $role, $level)) {
                continue;
            }

            if (!empty($item['children'])) {

                $children = array_values(array_filter(
                    $item['children'],
                    fn($child) => self::matches($child, $role, $level)
                ));

                if (empty($children)) {
                    continue;
                }

                $item['children'] = $children;
            }

            $filtered[] = $item;
        }

        return $filtered;
    }

    public static function getMenuForRole(string $role): array
    {
        return self::getMenuForRoleAndLevel($role, PHP_INT_MAX);
    }

    private static function matches(array $item, string $role, int $level): bool
    {
        $roleOk = in_array($role, $item['roles'] ?? [], true);

        $levelOk = !isset($item['levels'])
            || in_array($level, $item['levels'], true)
            || $level === PHP_INT_MAX;

        return $roleOk && $levelOk;
    }
}