@component('mail::message')
{{ __('You have been invited to join the :team group!', ['team' => $invitation->team->name]) }}

@if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::registration()))
{{ __('If you do not have an account, you may create one by clicking the button below:') }}

@component('mail::button', ['url' => route('filament.app.auth.register', ['email' => $invitation->email, 'team' => $invitation->team_id])])
{{ __('Create Account') }}
@endcomponent

{{ __('If you already have an account, you may accept this invitation by clicking the button below:') }}

@else
{{ __('You may accept this invitation by clicking the button below:') }}
@endif


@component('mail::button', ['url' => $acceptUrl])
{{ __('Accept Invitation') }}
@endcomponent

{{ __('If you did not expect to receive an invitation to this group, you may discard this email.') }}
@endcomponent
