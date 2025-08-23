<x-mail::message>
Contact Message

Name: {{ $contact->name }}
Email: {{ $contact->email }}
Message: {{ $contact->message }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
