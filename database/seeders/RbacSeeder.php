<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RoleModulePermission;
use App\Models\Submodule;
use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Core Roles
        $rolesData = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'Unrestricted root administrator with complete control over all modules and settings.',
                'is_system' => true,
            ],
            [
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'System administrator managing users, content, and daily operations.',
                'is_system' => true,
            ],
            [
                'name' => 'judge',
                'display_name' => 'Judge',
                'description' => 'Judicial officer with access to AI Chatbot, notifications, landmark judgments, and research.',
                'is_system' => true,
            ],
            [
                'name' => 'advocate',
                'display_name' => 'Advocate',
                'description' => 'Legal practitioner with access to AI Chatbot, notifications, case law, and legal resources.',
                'is_system' => true,
            ],
            [
                'name' => 'student',
                'display_name' => 'Student',
                'description' => 'Law student with access to examinations, mock tests, bare acts, and learning resources.',
                'is_system' => true,
            ],
            [
                'name' => 'public',
                'display_name' => 'Public',
                'description' => 'General public citizen seeking legal information, maxims, and public resources.',
                'is_system' => true,
            ],
        ];

        $roles = [];
        foreach ($rolesData as $data) {
            $roles[$data['name']] = Role::updateOrCreate(['name' => $data['name']], $data);
        }

        // 2. Modules & Submodules from Law Universe Master Specification
        $modulesStructure = [
            [
                'name' => 'Manage User Account',
                'slug' => 'manage_users',
                'icon' => 'bi-people-fill',
                'order' => 1,
                'description' => 'Manage user accounts, approvals, role permissions, and access status.',
                'submodules' => [
                    ['name' => 'Add User Account', 'slug' => 'add_user', 'route_name' => 'admin.users.create', 'order' => 1],
                    ['name' => 'List User Account', 'slug' => 'list_users', 'route_name' => 'admin.users.index', 'order' => 2],
                ],
            ],
            [
                'name' => 'Manage Resource',
                'slug' => 'manage_resources',
                'icon' => 'bi-folder2-open',
                'order' => 2,
                'description' => 'Upload and organize legal documents, bare acts, legal maxims, landmark judgments, and notes.',
                'submodules' => [
                    ['name' => 'Upload Resources', 'slug' => 'upload_resources', 'order' => 1],
                    ['name' => 'List Resources', 'slug' => 'list_resources', 'order' => 2],
                    ['name' => 'Bare Acts', 'slug' => 'bare_acts', 'order' => 3],
                    ['name' => 'Legal Maxims', 'slug' => 'legal_maxims', 'order' => 4],
                    ['name' => 'Landmark Judgement', 'slug' => 'landmark_judgement', 'order' => 5],
                    ['name' => 'Notes', 'slug' => 'notes', 'order' => 6],
                ],
            ],
            [
                'name' => 'Manage Examination',
                'slug' => 'manage_examinations',
                'icon' => 'bi-mortarboard-fill',
                'order' => 3,
                'description' => 'Manage mock examinations, question papers, previous years papers, categories, and labels.',
                'submodules' => [
                    ['name' => 'Examination', 'slug' => 'examination', 'order' => 1],
                    ['name' => 'Question Paper', 'slug' => 'question_paper', 'order' => 2],
                    ['name' => 'Previous Years Paper', 'slug' => 'previous_years_paper', 'order' => 3],
                    ['name' => 'Manage Categories', 'slug' => 'manage_categories', 'order' => 4],
                    ['name' => 'Manage Label', 'slug' => 'manage_label', 'order' => 5],
                ],
            ],
            [
                'name' => 'Manage Blogging',
                'slug' => 'manage_blogging',
                'icon' => 'bi-journal-richtext',
                'order' => 4,
                'description' => 'Compose and publish legal articles, monitor statistics, and manage published blogs.',
                'submodules' => [
                    ['name' => 'Blogs Statistics', 'slug' => 'blogs_statistics', 'order' => 1],
                    ['name' => 'Compose Blogs', 'slug' => 'compose_blogs', 'order' => 2],
                    ['name' => 'List Blogs', 'slug' => 'list_blogs', 'order' => 3],
                ],
            ],
            [
                'name' => 'Manage Finance',
                'slug' => 'manage_finance',
                'icon' => 'bi-cash-coin',
                'order' => 5,
                'description' => 'Subscription plans, pricing packages, feature access, coupons, and transaction ledger.',
                'submodules' => [
                    ['name' => 'Finance Statistics', 'slug' => 'finance_statistics', 'order' => 1],
                    ['name' => 'Manage Package', 'slug' => 'manage_package', 'order' => 2],
                    ['name' => 'Manage Features', 'slug' => 'manage_features', 'order' => 3],
                    ['name' => 'Manage Transaction', 'slug' => 'manage_transaction', 'order' => 4],
                    ['name' => 'Manage Subscription', 'slug' => 'manage_subscription', 'order' => 5],
                    ['name' => 'Manage Coupon', 'slug' => 'manage_coupon', 'order' => 6],
                ],
            ],
            [
                'name' => 'Manage AI Chatbot',
                'slug' => 'manage_ai_chatbot',
                'icon' => 'bi-robot',
                'order' => 6,
                'description' => 'Interact with AI legal research chatbot, review past conversations, and configure GPT engines.',
                'submodules' => [
                    ['name' => 'Ask AI Chatbot', 'slug' => 'ask_ai_chatbot', 'route_name' => 'chatbot.ask', 'order' => 1],
                    ['name' => 'AI Chatbot History', 'slug' => 'ai_chatbot_history', 'route_name' => 'chatbot.history', 'order' => 2],
                    ['name' => 'Manage GPT Engine API', 'slug' => 'manage_gpt_engine_api', 'order' => 3],
                ],
            ],
            [
                'name' => 'Manage Notification',
                'slug' => 'manage_notifications',
                'icon' => 'bi-bell-fill',
                'order' => 7,
                'description' => 'Broadcast announcements, notifications to specific user roles, and monitor delivery.',
                'submodules' => [
                    ['name' => 'List Notification', 'slug' => 'list_notification', 'route_name' => 'notifications.index', 'order' => 1],
                    ['name' => 'Post Notification', 'slug' => 'post_notification', 'route_name' => 'notifications.create', 'order' => 2],
                ],
            ],
            [
                'name' => 'Dynamic RBAC & Roles',
                'slug' => 'manage_rbac',
                'icon' => 'bi-shield-lock-fill',
                'order' => 8,
                'description' => 'Dynamic role permission matrix, module level access control, and user role assignments.',
                'submodules' => [
                    ['name' => 'Roles & Permissions Matrix', 'slug' => 'rbac_matrix', 'route_name' => 'admin.rbac.index', 'order' => 1],
                    ['name' => 'Audit & Activity Logs', 'slug' => 'audit_logs', 'route_name' => 'admin.rbac.logs', 'order' => 2],
                ],
            ],
        ];

        foreach ($modulesStructure as $modData) {
            $subData = $modData['submodules'];
            unset($modData['submodules']);

            $module = Module::updateOrCreate(['slug' => $modData['slug']], $modData);

            foreach ($subData as $s) {
                $submodule = Submodule::updateOrCreate(
                    ['module_id' => $module->id, 'slug' => $s['slug']],
                    [
                        'name' => $s['name'],
                        'route_name' => $s['route_name'] ?? null,
                        'order' => $s['order'] ?? 0,
                    ]
                );

                // Create standard permissions for this submodule
                $permPrefix = $s['slug'];
                $actions = ['view', 'create', 'edit', 'delete'];
                foreach ($actions as $act) {
                    Permission::updateOrCreate(
                        ['name' => "{$permPrefix}.{$act}"],
                        [
                            'module_id' => $module->id,
                            'submodule_id' => $submodule->id,
                            'display_name' => ucfirst($act) . ' ' . $s['name'],
                            'description' => "Allows user to {$act} in {$s['name']}",
                        ]
                    );
                }
            }
        }

        // Additional granular permissions
        $extraPermissions = [
            'users.approve' => 'Approve pending Advocate / Judge user accounts',
            'users.reject' => 'Reject pending user accounts',
            'users.deactivate' => 'Deactivate or reactivate user accounts',
            'users.bulk_delete' => 'Multi-Trash delete multiple user accounts',
            'rbac.assign' => 'Assign and update roles and permissions dynamically',
        ];

        $userModule = Module::where('slug', 'manage_users')->first();
        foreach ($extraPermissions as $permName => $desc) {
            Permission::updateOrCreate(
                ['name' => $permName],
                [
                    'module_id' => $userModule?->id,
                    'display_name' => ucwords(str_replace(['.', '_'], ' ', $permName)),
                    'description' => $desc,
                ]
            );
        }

        // 3. Default Role-Module Permissions Setup
        // Super Admin gets everything automatically via isSuperAdmin()

        // Admin gets view, create, edit, delete on all modules except rbac is view/edit only
        $allModules = Module::with('submodules')->get();
        foreach ($allModules as $mod) {
            RoleModulePermission::updateOrCreate(
                ['role_id' => $roles['admin']->id, 'module_id' => $mod->id, 'submodule_id' => null],
                ['can_view' => true, 'can_create' => true, 'can_edit' => true, 'can_delete' => true]
            );
            foreach ($mod->submodules as $sub) {
                RoleModulePermission::updateOrCreate(
                    ['role_id' => $roles['admin']->id, 'module_id' => $mod->id, 'submodule_id' => $sub->id],
                    ['can_view' => true, 'can_create' => true, 'can_edit' => true, 'can_delete' => true]
                );
            }
        }

        // Judge & Advocate get AI Chatbot, Notifications, Resources (view)
        $advocateJudgeRoles = [$roles['judge'], $roles['advocate']];
        $advocateJudgeModules = ['manage_ai_chatbot', 'manage_notifications', 'manage_resources'];

        foreach ($advocateJudgeRoles as $roleObj) {
            foreach ($advocateJudgeModules as $slug) {
                $mod = Module::where('slug', $slug)->first();
                if ($mod) {
                    RoleModulePermission::updateOrCreate(
                        ['role_id' => $roleObj->id, 'module_id' => $mod->id, 'submodule_id' => null],
                        ['can_view' => true, 'can_create' => true, 'can_edit' => false, 'can_delete' => false]
                    );
                    foreach ($mod->submodules as $sub) {
                        RoleModulePermission::updateOrCreate(
                            ['role_id' => $roleObj->id, 'module_id' => $mod->id, 'submodule_id' => $sub->id],
                            ['can_view' => true, 'can_create' => in_array($sub->slug, ['ask_ai_chatbot', 'notes']), 'can_edit' => false, 'can_delete' => false]
                        );
                    }
                }
            }
        }

        // Student gets Examinations (view/attempt), Resources (view), Notifications (view)
        $studentModules = ['manage_examinations', 'manage_resources', 'manage_notifications'];
        foreach ($studentModules as $slug) {
            $mod = Module::where('slug', $slug)->first();
            if ($mod) {
                RoleModulePermission::updateOrCreate(
                    ['role_id' => $roles['student']->id, 'module_id' => $mod->id, 'submodule_id' => null],
                    ['can_view' => true, 'can_create' => false, 'can_edit' => false, 'can_delete' => false]
                );
            }
        }

        // Public gets Bare Acts, Legal Maxims, Notifications (view)
        $publicMod = Module::where('slug', 'manage_resources')->first();
        if ($publicMod) {
            RoleModulePermission::updateOrCreate(
                ['role_id' => $roles['public']->id, 'module_id' => $publicMod->id, 'submodule_id' => null],
                ['can_view' => true, 'can_create' => false, 'can_edit' => false, 'can_delete' => false]
            );
        }
    }
}
