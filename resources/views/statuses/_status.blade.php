<li class="media mt-4 mb-4">
	<a href="{{ route('users.show', $user->id) }}">
		<img class="mr-3 gravatar" src="{{ $user->gravatar() }}" alt="{{ $user->name }}" />
	</a>
	<div class="media-body">
		<h5 class="mt-0">
			{{ $user->name }}
			<small> / {{ $status->created_at->diffForHumans() }}</small>
		</h5>	
		{{ $status->content }}
	</div>
</li>