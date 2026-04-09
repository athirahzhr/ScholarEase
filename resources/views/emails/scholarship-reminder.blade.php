<h3>⏰ Scholarship Deadline Reminder</h3>

<p>Hello {{ $user->name }},</p>

<p>
You bookmarked <strong>{{ $scholarship->title }}</strong>.
</p>

<p>
Deadline in <strong>{{ $daysLeft }} days</strong><br>
📅 {{ $scholarship->deadline->format('d M Y') }}
</p>

<a href="{{ $scholarship->application_link }}">
Apply Now
</a>

<p>This is an automated email. Please do not reply.</p>
