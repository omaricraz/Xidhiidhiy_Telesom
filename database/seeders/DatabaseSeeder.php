<?php

namespace Database\Seeders;

use App\Models\LearningGoal;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Models\UserLearningProgress;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Teams
        $devTeam = Team::create([
            'name' => 'Dev',
        ]);

        $devOpsTeam = Team::create([
            'name' => 'DevOps',
        ]);

        // Create Manager (User #1) leading both teams
        $manager = User::create([
            'name' => 'Manager',
            'full_name' => 'John Manager',
            'email' => 'manager@xidhiidhiye.com',
            'password' => Hash::make('password'),
            'role' => 'Manager',
            'team_id' => null,
            'tech_stack' => 'Laravel, PHP, MySQL, JavaScript, Vue.js',
            'status_emoji' => '👔',
        ]);

        // Update teams with lead_id
        $devTeam->update(['lead_id' => $manager->id]);
        $devOpsTeam->update(['lead_id' => $manager->id]);

        // Create Team Leads
        $devLead = User::create([
            'name' => 'Sarah: Dev Lead',
            'full_name' => 'Sarah Dev Lead',
            'email' => 'devlead@xidhiidhiye.com',
            'password' => Hash::make('password'),
            'role' => 'Team_Lead',
            'team_id' => $devTeam->id,
            'tech_stack' => 'Laravel, PHP, React, TypeScript',
            'status_emoji' => '🚀',
        ]);

        $devOpsLead = User::create([
            'name' => 'Mike: DevOps Lead',
            'full_name' => 'Mike DevOps Lead',
            'email' => 'devopslead@xidhiidhiye.com',
            'password' => Hash::make('password'),
            'role' => 'Team_Lead',
            'team_id' => $devOpsTeam->id,
            'tech_stack' => 'Docker, Kubernetes, AWS, Terraform',
            'status_emoji' => '⚙️',
        ]);

        // Create 2 Interns per team
        $devIntern1 = User::create([
            'name' => 'Alice: Dev Intern',
            'full_name' => 'Alice Developer',
            'email' => 'devintern1@xidhiidhiye.com',
            'password' => Hash::make('password'),
            'role' => 'Intern',
            'team_id' => $devTeam->id,
            'tech_stack' => 'PHP, JavaScript',
            'status_emoji' => '🌱',
        ]);

        $devIntern2 = User::create([
            'name' => 'Bob: Dev Intern',
            'full_name' => 'Bob Developer',
            'email' => 'devintern2@xidhiidhiye.com',
            'password' => Hash::make('password'),
            'role' => 'Intern',
            'team_id' => $devTeam->id,
            'tech_stack' => 'HTML, CSS, JavaScript',
            'status_emoji' => '🌱',
        ]);

        $devOpsIntern1 = User::create([
            'name' => 'Charlie: DevOps Intern',
            'full_name' => 'Charlie DevOps',
            'email' => 'devopsintern1@xidhiidhiye.com',
            'password' => Hash::make('password'),
            'role' => 'Intern',
            'team_id' => $devOpsTeam->id,
            'tech_stack' => 'Linux, Bash, Docker',
            'status_emoji' => '🌱',
        ]);

        $devOpsIntern2 = User::create([
            'name' => 'Diana: DevOps Intern',
            'full_name' => 'Diana DevOps',
            'email' => 'devopsintern2@xidhiidhiye.com',
            'password' => Hash::make('password'),
            'role' => 'Intern',
            'team_id' => $devOpsTeam->id,
            'tech_stack' => 'Python, Docker, Git',
            'status_emoji' => '🌱',
        ]);

        // Create 3 Learning Goals per team
        $devGoals = [
            [
                'team_id' => $devTeam->id,
                'title' => 'Laravel Fundamentals',
                'description' => 'Learn the basics of Laravel framework including routing, controllers, and models.',
                'resource_url' => 'https://laravel.com/docs',
            ],
            [
                'team_id' => $devTeam->id,
                'title' => 'Vue.js Basics',
                'description' => 'Understand Vue.js components, directives, and state management.',
                'resource_url' => 'https://vuejs.org/guide/',
            ],
            [
                'team_id' => $devTeam->id,
                'title' => 'Database Design',
                'description' => 'Learn database normalization, relationships, and query optimization.',
                'resource_url' => 'https://www.postgresql.org/docs/',
            ],
        ];

        $devOpsGoals = [
            [
                'team_id' => $devOpsTeam->id,
                'title' => 'Docker Essentials',
                'description' => 'Master Docker containers, images, and Docker Compose.',
                'resource_url' => 'https://docs.docker.com/',
            ],
            [
                'team_id' => $devOpsTeam->id,
                'title' => 'Kubernetes Basics',
                'description' => 'Learn Kubernetes pods, services, and deployments.',
                'resource_url' => 'https://kubernetes.io/docs/',
            ],
            [
                'team_id' => $devOpsTeam->id,
                'title' => 'CI/CD Pipeline',
                'description' => 'Understand continuous integration and deployment workflows.',
                'resource_url' => 'https://www.jenkins.io/doc/',
            ],
        ];

        foreach ($devGoals as $goalData) {
            $goal = LearningGoal::create($goalData);
            // Create progress entries for all team members
            foreach ([$devLead, $devIntern1, $devIntern2] as $member) {
                UserLearningProgress::create([
                    'user_id' => $member->id,
                    'goal_id' => $goal->id,
                    'is_completed' => false,
                ]);
            }
        }

        foreach ($devOpsGoals as $goalData) {
            $goal = LearningGoal::create($goalData);
            // Create progress entries for all team members
            foreach ([$devOpsLead, $devOpsIntern1, $devOpsIntern2] as $member) {
                UserLearningProgress::create([
                    'user_id' => $member->id,
                    'goal_id' => $goal->id,
                    'is_completed' => false,
                ]);
            }
        }

        // Create sample tasks for interns
        Task::create([
            'title' => 'Setup Development Environment',
            'description' => 'Install and configure Laravel, PHP, and required tools.',
            'priority' => 'High',
            'status' => 'Pending',
            'is_private' => false,
            'creator_id' => $devLead->id,
            'assignee_id' => $devIntern1->id,
        ]);

        Task::create([
            'title' => 'Learn Laravel Routing',
            'description' => 'Complete the Laravel routing tutorial and create sample routes.',
            'priority' => 'Medium',
            'status' => 'In_Progress',
            'is_private' => false,
            'creator_id' => $devLead->id,
            'assignee_id' => $devIntern2->id,
        ]);

        Task::create([
            'title' => 'Docker Container Setup',
            'description' => 'Create Docker containers for development environment.',
            'priority' => 'High',
            'status' => 'Pending',
            'is_private' => false,
            'creator_id' => $devOpsLead->id,
            'assignee_id' => $devOpsIntern1->id,
        ]);

        Task::create([
            'title' => 'Kubernetes Cluster Configuration',
            'description' => 'Set up a local Kubernetes cluster and deploy sample applications.',
            'priority' => 'Medium',
            'status' => 'Pending',
            'is_private' => false,
            'creator_id' => $devOpsLead->id,
            'assignee_id' => $devOpsIntern2->id,
        ]);
    }
}
