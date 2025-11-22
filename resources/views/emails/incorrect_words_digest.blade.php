@component('mail::message')
# Frequently Incorrect Words

@if($user)
Hi {{ $user->name }},
@endif

Here’s your digest of words you often miss. Keep practicing and you’ll see steady improvement.

- Tip: Revisit these words in your Review session.
- We’ll make these summaries smarter as your activity grows.

@component('mail::button', ['url' => url('/review/practice')])
Practice Now
@endcomponent

Want to adjust how often you get this? Update your preferences in your profile.

Thanks,
The Daily Vocabulary Team
@endcomponent
