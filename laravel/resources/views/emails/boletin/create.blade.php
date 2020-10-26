@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level == 'error')
# Whoops!
@else
# Hola!
@endif
@endif

Te has suscrito al boletín correctamente.

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
Saludos,<br>{{ config('app.name') }}
@endif

@component('mail::subcopy')
[Desuscribirse]({{$url}}) del boletín.
@endcomponent

@endcomponent
