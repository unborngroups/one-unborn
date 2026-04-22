<x-mail::message>
# Hello {{ $user->name }}

Your password has been successfully changed.

If you did not make this change, please contact our support immediately.

Thanks,  
{{ config('app.name') }} Security Team
</x-mail::message>
