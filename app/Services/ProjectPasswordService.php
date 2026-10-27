<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectPasswordAudit;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProjectPasswordService
{
    /**
     * Get the plain text password for a project
     */
    public function getPlainPassword(Project $project): ?string
    {
        if (! $project->project_admin_password_plain) {
            return null;
        }

        try {
            return decrypt($project->project_admin_password_plain);
        } catch (\Exception $e) {
            Log::error('Failed to decrypt password for project '.$project->id.': '.$e->getMessage());

            return null;
        }
    }

    /**
     * Set a new password for the project
     */
    public function setPassword(Project $project, string $password, User $updatedBy): bool
    {
        try {
            // Update both hashed and encrypted plain password
            $project->project_admin_password = Hash::make($password);
            $project->project_admin_password_plain = encrypt($password);
            $project->password_updated_at = now();
            $project->password_updated_by = $updatedBy->id;

            $saved = $project->save();

            if ($saved) {
                $this->logPasswordAccess($project, $updatedBy, 'updated');
            }

            return $saved;
        } catch (\Exception $e) {
            Log::error('Failed to set password for project '.$project->id.': '.$e->getMessage());

            return false;
        }
    }

    /**
     * Generate a new random password for the project
     */
    public function generatePassword(Project $project, User $updatedBy): string
    {
        $password = $this->generateSecurePassword();

        if ($this->setPassword($project, $password, $updatedBy)) {
            $this->logPasswordAccess($project, $updatedBy, 'generated');

            return $password;
        }

        throw new \Exception('Failed to generate password for project '.$project->id);
    }

    /**
     * Log password access for audit purposes
     */
    public function logPasswordAccess(Project $project, User $user, string $action): void
    {
        try {
            ProjectPasswordAudit::create([
                'project_id' => $project->id,
                'user_id' => $user->id,
                'action' => $action,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'performed_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log password access for project '.$project->id.': '.$e->getMessage());
        }
    }

    /**
     * Validate password strength
     */
    public function validatePassword(string $password): array
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }

        if (! preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }

        if (! preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }

        if (! preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }

        if (! preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }

        return $errors;
    }

    /**
     * Generate a secure random password (public method for testing)
     */
    public function generateSecurePassword(int $length = 12): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%^&*';

        // Ensure at least one character from each category
        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];

        // Fill the rest with random characters
        $allChars = $uppercase.$lowercase.$numbers.$special;
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Shuffle the password to randomize character positions
        return str_shuffle($password);
    }

    /**
     * Check if a project has a viewable password
     */
    public function hasViewablePassword(Project $project): bool
    {
        return ! empty($project->project_admin_password_plain);
    }

    /**
     * Get password audit history for a project
     */
    public function getPasswordAuditHistory(Project $project, int $limit = 50): Collection
    {
        return $project->passwordAudits()
            ->with('user')
            ->orderBy('performed_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Encrypt existing plain passwords for projects that don't have encrypted versions
     */
    public function migrateExistingPasswords(): int
    {
        $migrated = 0;

        // Find projects that have plain passwords but no encrypted version
        $projects = Project::whereNotNull('project_admin_password')
            ->whereNull('project_admin_password_plain')
            ->get();

        foreach ($projects as $project) {
            // For existing projects, we can't recover the original password from hash
            // So we generate a new one
            try {
                $newPassword = $this->generateSecurePassword();
                $project->project_admin_password = Hash::make($newPassword);
                $project->project_admin_password_plain = encrypt($newPassword);
                $project->password_updated_at = now();
                $project->save();

                $migrated++;
                Log::info("Generated new password for project {$project->id} ({$project->name})");
            } catch (\Exception $e) {
                Log::error("Failed to migrate password for project {$project->id}: ".$e->getMessage());
            }
        }

        return $migrated;
    }
}
