<html>
    <body>
        <h1>{{ ucfirst($type) }} transfer</h1>

        @if (!empty($target_user))
            <p>Hello, {{ $target_user->name }},</p>
        @endif

        <p><a href="https://htch.us">Hutch</a> user {{ $source_user->name }} asked us to transfer one of their {{ $type_plural }} to your account.</p>

        <p>Authorize this transfer and to claim the {{ $type }} here: <a href="{{ $url }}">{{ $url }}</a></p>

        <br>
        <hr>
        @if (!empty($target_user))
            <p>Login to view and manage Hutch: <a href="https://htch.us">https://htch.us</a></p>
            <br>
            <p>Thanks for choosing <a href="http://barntrax.com">Hutch</a></p>
        @else
            <p>Register here to gain access to the Hutch: <a href="{{ route('web.invite', [ 'inviter' => $source_user->getSlug() ], true) }}">https://htch.us/register</a></p>
        @endif
    </body>
</html>
