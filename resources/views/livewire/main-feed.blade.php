<?php
use function Livewire\Volt\{state, with};
use App\Models\Post;

state(['body' => '']);

$post = function () {
    if (!$this->body) return;

    $user = \App\Models\User::first() ?? \App\Models\User::factory()->create();
    
    $user->posts()->create([
        'body' => $this->body,
    ]);

    $this->body = ''; 
};

with(fn () => [
    'posts' => Post::with('user')->latest()->get()
]);
?>

<div class="space-y-6">
    <div class="bg-white p-4 rounded-xl shadow border">
        <textarea wire:model="body" class="w-full border-none focus:ring-0 text-lg" placeholder="What's happening?"></textarea>
        <div class="flex justify-end pt-2">
            <button wire:click="post" class="bg-blue-500 text-white px-6 py-2 rounded-full font-bold">
                Post
            </button>
        </div>
    </div>

    <div class="space-y-4">
        @foreach ($posts as $post)
            <div class="bg-white p-4 rounded-xl shadow border">
                <div class="font-bold text-blue-600">{{ $post->user->name }}</div>
                <p class="text-gray-800 mt-2">{{ $post->body }}</p>
                <div class="text-xs text-gray-400 mt-2">{{ $post->created_at->diffForHumans() }}</div>
            </div>
        @endforeach
    </div>
</div>