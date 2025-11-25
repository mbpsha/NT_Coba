@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
<div style="font-size: 32px; font-weight: bold; color: #16a34a; font-family: Arial, sans-serif; letter-spacing: -0.5px;">
    🌾 NGUNDUR
</div>
@endif
</a>
</td>
</tr>
