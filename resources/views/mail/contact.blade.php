<x-mail::message>
# New Contact Form Submission

**From:** {{ $contact->name }}

**Email:** {{ $contact->email }}

**Subject:** {{ $contact->subject ?? 'Contact Message' }}

**Message:**

{{ $contact->message }}

---

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
