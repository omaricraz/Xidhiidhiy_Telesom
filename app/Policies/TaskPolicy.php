<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine if the user can view any tasks.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view tasks (scoped in controller)
    }

    /**
     * Determine if the user can view the task.
     */
    public function view(User $user, Task $task): bool
    {
        // Manager can view all
        if ($user->isManager()) {
            return true;
        }

        // Team Lead can view tasks from their team
        if ($user->isTeamLead() && $user->team_id) {
            return $task->assignee && $task->assignee->team_id === $user->team_id;
        }

        // Intern can only view their own tasks
        if ($user->isIntern()) {
            return $task->assignee_id === $user->id || $task->creator_id === $user->id;
        }

        // Employee can view their own tasks or tasks assigned to them
        return $task->assignee_id === $user->id || $task->creator_id === $user->id;
    }

    /**
     * Determine if the user can create tasks.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create tasks
    }

    /**
     * Determine if the user can update the task.
     */
    public function update(User $user, Task $task): bool
    {
        // Manager can update all
        if ($user->isManager()) {
            return true;
        }

        // Team Lead can update tasks from their team
        if ($user->isTeamLead() && $user->team_id) {
            return $task->assignee && $task->assignee->team_id === $user->team_id;
        }

        // Intern can only update status of their own tasks
        if ($user->isIntern()) {
            return $task->assignee_id === $user->id;
        }

        // Employee can update their own tasks
        return $task->assignee_id === $user->id || $task->creator_id === $user->id;
    }

    /**
     * Determine if the user can delete the task.
     */
    public function delete(User $user, Task $task): bool
    {
        // Manager can delete all
        if ($user->isManager()) {
            return true;
        }

        // Team Lead can delete tasks from their team
        if ($user->isTeamLead() && $user->team_id) {
            return $task->assignee && $task->assignee->team_id === $user->team_id;
        }

        // Intern cannot delete
        if ($user->isIntern()) {
            return false;
        }

        // Employee can delete their own tasks
        return $task->creator_id === $user->id;
    }
}

