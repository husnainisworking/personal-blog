<!-- This uses Breeze's guest layout (header, styling, etc.) -->
<x-guest-layout>
{{--
 x-something means "load a component"
 The "x-" tells Laravel: "this is a component, not regular HTML"
 <x-guest-layout> : This is a special Laravel feature for reusable HTML templates.
--}}

    <div class="mb-4 text-sm text-gray-600">

        {{ __('We sent a verification code to your email. Please enter it below.') }}
        {{-- __() is Laravel's translation function --}}
    </div>

    {{-- If there are any errors, show them in red--}}
    @if($errors->any())
        <div class="mb-4 text-sm text-red-600">
            <ul>

                @foreach($errors->all() as $error)
                    {{--$errors is an object: an instance of Illuminate\Support\ViewErrorBag
                    $errors is a ViewErrorBag object, which is basically a wrapper around one or more MessageBag objects.
                    --}}
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- The actual form starts here--}}
    {{-- When submitted, it will POST to the '2fa.verify' route --}}
    <form method="POST" action="{{ route('2fa.verify') }}">
        {{-- Laravel security feature to prevent attacks --}}
        @csrf

        <div>
            {{-- Label for the input field --}}
            <x-input-label for="code" :value="__('Verification Code')" />
            {{-- The actual input field where user types the code --}}
            <x-text-input
                id="code" {{-- HTML id for this input--}}
                class="block mt-1 w-full text-center text-2xl tracking-widest" {{-- Tailwind CSS styling--}}
                type="text" {{-- Input type --}}
                name="code" {{-- This is what gets sent to the controller as $request->code --}}
                maxlength="6"
                required
                autofocus
                placeholder="000000" {{-- Gray text showing what to type --}}
            />
            {{-- Show validation errors for this specific field --}}
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        {{--Submit button area--}}
        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Verify') }} {{-- Button text --}}
            </x-primary-button>
        </div>

        {{-- Info message at the bottom --}}
        <div class="mt-4 text-sm text-gray-600 text-center">
            Code expires in 10 minutes
        </div>
    </form>

    {{-- NEW: Resend button (separate form)--}}
    <div class="mt-6 text-center">
        <form method="POST" action="{{route('2fa.resend')}}">
            @csrf
            <button type="submit" class="text-sm text-gray-600 underline hover:text-gray-900">
                Didn't receive the code? Click here to resend
            </button>
        </form>
    </div>
</x-guest-layout>















