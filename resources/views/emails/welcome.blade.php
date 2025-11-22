@component('mail::message')
# Welcome to Daily Vocabulary!

@if($user)
Hi {{ $user->name }},
@else
Hi there,
@endif

Thanks for subscribing. Here’s what to expect:

- A steady stream of vocabulary to strengthen your skills.
- Optional digests for frequently incorrect words and topic summaries.
- You can adjust preferences any time in your profile.

@component('mail::button', ['url' => url('/dashboard')])
Go to Dashboard
@endcomponent

@if($user)
@component('mail::button', ['url' => route('profile.edit')])
Manage Preferences
@endcomponent
@endif

If this wasn’t you or you’d like to stop emails, you can unsubscribe from the profile page or reply to this email and we’ll help.

Thanks,
The Daily Vocabulary Team
@endcomponent
