@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel' || trim($slot) === config('app.name'))
<img src="{{ config('app.url') }}/imgs/workfit_logo_black.png" class="logo" alt="{{ config('app.name') }} Logo" style="max-height: 50px; height: auto;">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
