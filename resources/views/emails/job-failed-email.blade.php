@component('mail::message')
# Uh oh! A job has failed

Hi, {{ $user->fname }}!


There has been a failed job with the following details:

Job: {{ $event->job }}
Connection Name: {{ $event->connectionName }}

@component('mail::panel')
{{ $event->exception }}
@endcomponent

{{ config('app.name') }}
@endcomponent