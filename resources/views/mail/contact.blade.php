<x-mail::message>
    <x-logo email width="200" height="60" />
Contact Message

Name: {{ $contact->name }}
Email: {{ $contact->email }}
Message: {{ $contact->message }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
