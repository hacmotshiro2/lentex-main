<div id="notification">
        <!-- Notification -->
        @if($showNF)
        <div class="fixed bottom-4 right-4 bg-green-500 text-white p-4 rounded-lg shadow-lg max-w-xs ">
            <p class="font-semibold">{{ $message }}</p>
        </div>
        @endif
</div>