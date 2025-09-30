<x-mail::message>
    <x-logo class="h-12" />
Contact Message

Name: {{ $contact->name }}
Email: {{ $contact->email }}
Message: {{ $contact->message }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
