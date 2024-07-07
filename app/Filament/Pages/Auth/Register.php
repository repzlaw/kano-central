<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use App\Models\TeamUser;
use App\Models\TeamInvitation;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Filament\Events\Auth\Registered;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Component;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
 
class Register extends BaseRegister
{
    protected function getForms(): array
    {
        $email = request()->query('email');
        $team = request()->query('team');

        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent()
                        ->default($email),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        Hidden::make('team_id')
                            ->default($team),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/register.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/register.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/register.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }

        $input = $this->form->getState();

        $team_invitation = TeamInvitation::where('email', strtolower($input['email']))
            ->when(isset($input['team_id']), function ($query) use ($input) {
                return $query->where('team_id', $input['team_id']);
            })
            ->first();

        if (!$team_invitation) {
            Notification::make()
                ->title('This email is not invited')
                ->danger()
                ->send();

            return null;
        }

        $user = DB::transaction(function () {
            $input = $this->form->getState();

            return $this->getUserModel()::create($input);
        });

        $this->createTeam($user, $team_invitation);

        // event(new Registered($user));

        $this->sendEmailVerificationNotification($user);

        Filament::auth()->login($user);

        session()->regenerate();

        return app(RegistrationResponse::class);
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