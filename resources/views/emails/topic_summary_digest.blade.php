@component('mail::message')
# Topic Summary

@if($user)
Hi {{ $user->name }},
@endif

Here’s a quick snapshot of your learning topics and recent activity.

- Stay consistent to deepen mastery.
- We’ll enrich these summaries with more insights over time.

@component('mail::button', ['url' => url('/dashboard')])
View Progress
@endcomponent

Want to adjust how often you get this? Update your preferences in your profile.

Thanks,
The Daily Vocabulary Team
@endcomponent
