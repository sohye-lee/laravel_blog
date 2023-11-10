<x-profile :sharedData="$sharedData">
  <div class="list-group">
    @foreach($followings as $following)
    <a href="/post/{{$following->userFollowed->id}}" class="list-group-item list-group-item-action">
      <img class="avatar-tiny" src="{{$following->userFollowed->avatar}}" />
      {{-- <strong>{{$post->username}}</strong> on {{$post->created_at->format('m/j/Y')}} --}}
      {{$following->userFollowed->username}}
    </a>
    @endforeach
  </div>
</x-profile>