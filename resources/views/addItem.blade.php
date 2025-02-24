<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                <form method="post" action="{{ route('product.store') }}" class="mt-6 space-y-6">
        @csrf
        @method('post')

        <div>
            <x-input-label for="add_url" :value="__('Product Url')" />
            <x-text-input id="add_url" name="url" type="password" class="mt-1 block w-full" autocomplete="url" />
            <x-input-error :messages="$errors->addProduct->get('url')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Add') }}</x-primary-button>
        </div>
    </form>                </div>
            </div>
        </div>
    </div>
</x-app-layout>
