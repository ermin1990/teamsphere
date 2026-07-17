{{-- Google sign-in via Firebase Authentication - shared by login.blade.php and register.blade.php. --}}
<div class="flex items-center gap-3 my-1">
    <div class="flex-1 h-px bg-outline-variant"></div>
    <span class="font-body-sm text-body-sm text-on-surface-variant uppercase">ili</span>
    <div class="flex-1 h-px bg-outline-variant"></div>
</div>

<button type="button" id="google-signin-btn"
        class="w-full h-14 bg-surface-container-lowest border border-outline-variant rounded-lg flex items-center justify-center gap-3 text-on-surface font-label-bold hover:border-primary transition-colors">
    <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
        <path fill="#4285F4" d="M19.6 10.23c0-.68-.06-1.36-.18-2.02H10v3.82h5.38a4.6 4.6 0 01-2 3.02v2.5h3.24c1.9-1.75 2.98-4.34 2.98-7.32z"/>
        <path fill="#34A853" d="M10 20c2.7 0 4.96-.89 6.62-2.42l-3.24-2.5c-.9.6-2.05.96-3.38.96-2.6 0-4.8-1.75-5.59-4.11H1.06v2.58A10 10 0 0010 20z"/>
        <path fill="#FBBC05" d="M4.41 11.93A5.99 5.99 0 014.1 10c0-.67.11-1.32.31-1.93V5.49H1.06A10 10 0 000 10c0 1.61.39 3.14 1.06 4.51l3.35-2.58z"/>
        <path fill="#EA4335" d="M10 3.96c1.47 0 2.79.5 3.82 1.49l2.87-2.87C14.95.99 12.7 0 10 0 6.09 0 2.7 2.24 1.06 5.49l3.35 2.58C5.2 5.71 7.4 3.96 10 3.96z"/>
    </svg>
    <span id="google-signin-text">Nastavi sa Google nalogom</span>
</button>

<form id="google-signin-form" method="POST" action="{{ route('google.handle') }}" class="hidden">
    @csrf
    <input type="hidden" name="id_token" id="google-id-token">
</form>

<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.13.0/firebase-app.js";
    import { getAuth, GoogleAuthProvider, signInWithPopup } from "https://www.gstatic.com/firebasejs/10.13.0/firebase-auth.js";

    const app = initializeApp({
        apiKey: @json(config('services.firebase.api_key')),
        authDomain: @json(config('services.firebase.auth_domain')),
        projectId: @json(config('services.firebase.project_id')),
        storageBucket: @json(config('services.firebase.storage_bucket')),
        messagingSenderId: @json(config('services.firebase.messaging_sender_id')),
        appId: @json(config('services.firebase.app_id')),
    });
    const auth = getAuth(app);

    const btn = document.getElementById('google-signin-btn');
    const btnText = document.getElementById('google-signin-text');

    btn.addEventListener('click', async function () {
        btn.disabled = true;
        const original = btnText.textContent;
        btnText.textContent = 'Povezivanje...';

        try {
            const result = await signInWithPopup(auth, new GoogleAuthProvider());
            const idToken = await result.user.getIdToken();
            document.getElementById('google-id-token').value = idToken;
            document.getElementById('google-signin-form').submit();
        } catch (err) {
            console.error(err);
            btn.disabled = false;
            btnText.textContent = original;
            if (err?.code !== 'auth/popup-closed-by-user' && err?.code !== 'auth/cancelled-popup-request') {
                alert('Google prijava nije uspjela. Pokušajte ponovo.');
            }
        }
    });
</script>
