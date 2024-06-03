<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\TeamUser;
use App\Models\TeamInvitation;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        $team_invitation = TeamInvitation::where('email', strtolower($input['email']))
            ->when(isset($input['team_id']), function ($query) use ($input) {
                return $query->where('team_id', $input['team_id']);
            })
            ->first();

        if (!$team_invitation) {
            $error = \Illuminate\Validation\ValidationException::withMessages([
               'email' => ['This email is not invited']
            ]);
            throw $error;
        }

        return DB::transaction(function () use ($input, $team_invitation) {
            return tap(User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]), function (User $user) use ($team_invitation) {
                $this->createTeam($user, $team_invitation);
            });
        });
    }

    /**
     * Create a personal team for the user.
     */
    protected function createTeam(User $user, TeamInvitation $team_invitation): void
    {
        TeamUser::create([
            'user_id' => $user->id,
            'team_id' => $team_invitation->team_id,
            'role'    => $team_invitation->role,
        ]);

        $user->current_team_id = $team_invitation->team_id;
        $user->save();

        $team_invitation->delete();
    }
}
