<?php
use function Livewire\Volt\{state, with, usesFileUploads};
use App\Models\Post;
use App\Models\Reactions;
use App\Models\Comment;
use App\Models\User;

usesFileUploads();

state([
    'body' => '',
    'image' => null,
    'commentBodies' => []
]);

$post = function () {
    if (!$this->body && !$this->image) return;

    $user = User::first() ?? User::factory()->create();
    
    $imagePath = null;
    if ($this->image) {
        $imagePath = $this->image->store('posts', 'public');
    }

    $user->posts()->create([
        'body' => $this->body,
        'image_path' => $imagePath,
    ]);

    $this->body = ''; 
    $this->image = null; 
};

$toggleReactions = function ($postId) {
    $user = User::first() ?? User::factory()->create();
    
    $existingReactions = Reactions::where('post_id', $postId)->where('user_id', $user->id)->first();
    
    if ($existingReactions) {
        $existingReactions->delete();
    } else {
        Reactions::create([
            'post_id' => $postId,
            'user_id' => $user->id,
            'type' => 'like'
        ]);
    }
};

$addComment = function ($postId) {
    $body = $this->commentBodies[$postId] ?? '';
    if (!trim($body)) return;

    $user = User::first() ?? User::factory()->create();

    Comment::create([
        'post_id' => $postId,
        'user_id' => $user->id,
        'body' => $body
    ]);

    $this->commentBodies[$postId] = '';
};

with(fn () => [
    'posts' => Post::with(['user', 'comments.user', 'reactions'])->latest()->get(),
    'currentUser' => User::first()
]);
?>

<div class="max-w-xl mx-auto space-y-6 pb-12">
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
        <textarea wire:model="body" class="w-full border-none focus:ring-0 text-gray-700 text-lg resize-none" placeholder="Share something new..."></textarea>
        
        @if ($image)
            <div class="mt-2 relative">
                <img src="{{ $image->temporaryUrl() }}" class="rounded-xl max-h-60 w-full object-cover">
                <button wire:click="$set('image', null)" class="absolute top-2 right-2 bg-black bg-opacity-50 text-white rounded-full p-1 text-xs">✕</button>
            </div>
        @endif

        <div class="flex justify-between items-center pt-4 border-t border-gray-50 mt-2">
            <label class="cursor-pointer text-gray-500 hover:text-blue-500 transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 00-1.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375 0 11-.75 0 .375 0 01.75 0z" />
                </svg>
                <span class="text-sm font-medium">Add Photo</span>
                <input type="file" wire:model="image" class="hidden" accept="image/*">
            </label>

            <button wire:click="post" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl font-semibold transition text-sm shadow-sm">
                Publish
            </button>
        </div>
    </div>

    <div class="space-y-4">
        @foreach ($posts as $post)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold uppercase text-sm">
                            {{ substr($post->user->name ?? 'U', 0, 2) }}
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">{{ $post->user->name ?? 'Anonymous' }}</div>
                            <div class="text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</div>
                        </div>
                    </div>

                    <p class="text-gray-700 mt-4 leading-relaxed">{{ $post->body }}</p>

                    @if ($post->image_path)
                        <div class="mt-4 rounded-xl overflow-hidden border border-gray-50 max-h-96">
                            <img src="{{ asset('storage/' . $post->image_path) }}" class="w-full h-full object-cover">
                        </div>
                    @endif

                    <div class="flex items-center gap-6 mt-5 pt-3 border-t border-gray-50 text-sm text-gray-500">
                        @php
                            // Fixed: Matched relationship name to 'reactions' cleanly
                            $reactionsCollection = $post->reactions ?? collect();
                            $isReacted = $currentUser ? $reactionsCollection->contains('user_id', $currentUser->id) : false;
                        @endphp
                        
                        <button wire:click="toggleReactions('{{ $post->id }}')" class="flex items-center gap-2 hover:text-red-500 transition {{ $isReacted ? 'text-red-500 font-medium' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="{{ $isReacted ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            </svg>
                            <span>{{ $reactionsCollection->count() }}</span>
                        </button>

                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 01-.923 1.785A5.969 5.969 0 006 21c1.282 0 2.47-.402 3.445-1.093.377.243.783.428 1.205.492z" />
                            </svg>
                            <span>{{ $post->comments->count() }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 border-t border-gray-100 p-5 space-y-4">
                    <div class="flex gap-2">
                        <input type="text" wire:model="commentBodies.{{ $post->id }}" wire:keydown.enter="addComment('{{ $post->id }}')" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="Write a comment...">
                        <button wire:click="addComment('{{ $post->id }}')" class="bg-gray-800 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-gray-900 transition">Reply</button>
                    </div>

                    @if($post->comments->count() > 0)
                        <div class="space-y-3 pt-2">
                            @foreach ($post->comments as $comment)
                                <div class="flex gap-2 items-start text-sm">
                                    <div class="w-7 h-7 rounded-full bg-gray-300 flex items-center justify-center font-bold text-gray-600 text-xs uppercase shrink-0">
                                        {{ substr($comment->user->name ?? 'U', 0, 2) }}
                                    </div>
                                    <div class="bg-white p-3 rounded-xl border border-gray-100 flex-1">
                                        <div class="flex justify-between items-center">
                                            <span class="font-semibold text-gray-800">{{ $comment->user->name ?? 'Anonymous' }}</span>
                                            <span class="text-xxs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-gray-600 mt-1">{{ $comment->body }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>