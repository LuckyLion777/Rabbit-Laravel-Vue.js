<html>
    <body>
        <h1>Hutch Weekly Digest</h1>
        <br>
        <p>Hello, {{ $user->name }}, here are your tasks from <a href="https://htch.us">Hutch</a>:</p>

        <h2>Upcoming Tasks</h2>
        @foreach($user->actualSevenDaysEvents as $task)
            <p>{{ $task->date }} - {{ $task->FullName }}</p>
        @endforeach

        <h2>Expired Tasks</h2>
        @foreach($user->expiredEvents as $task)
            <p>{{ $task->date }} - {{ $task->FullName }}</p>
        @endforeach

        <br>
        <hr>
		<p>Sync your Schedule with Calendar programs and apps! 
        <br>Learn More: <a href="http://support.barntrax.com/index.php?p=/discussion/247/schedule-sync-with-calendar-programs">http://support.barntrax.com/index.php?p=/discussion/247/schedule-sync-with-calendar-programs</a></p>
        <hr>
		<p>Login to view and manage Hutch: <a href="https://htch.us">https://htch.us</a></p>
        <p>To stop receiving these emails, turn off Weekly Digest under Settings.</p>
        <br>
        <p>Thanks for choosing <a href="http://barntrax.com">Hutch</a></p>
    </body>
</html>
