<x-guest-layout>
    <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-4 col-lg-5">
                    <div class="card">

                        <!-- Logo -->
                        <div class="card-header pt-4 pb-4 text-center bg-primary">
                            <a href="/" class="text-decoration-none">
                                <span class="text-white fs-3 fw-bold">Multimight Computindo</span>
                            </a>
                        </div>

                        <div class="card-body p-4">
                            
                            <div class="text-center w-75 m-auto">
                                <h4 class="text-dark-50 text-center mt-0 fw-bold">Reset Password</h4>
                                <p class="text-muted mb-4">Enter your email address and we'll send you an email with instructions to reset your password.</p>
                            </div>

                            <!-- Session Status -->
                            <x-auth-session-status class="mb-4 text-success text-center" :status="session('status')" />

                            <form method="POST" action="{{ route('password.email') }}">
                                @csrf

                                <!-- Email Address -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email address</label>
                                    <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter your email">
                                    <x-input-error :messages="$errors->get('email')" class="invalid-feedback mt-2" />
                                </div>

                                <!-- Submit Button -->
                                <div class="mb-0 text-center">
                                    <button class="btn btn-primary w-100" type="submit">Email Password Reset Link</button>
                                </div>

                            </form>
                        </div> <!-- end card-body-->
                    </div>
                    <!-- end card -->

                    <!-- Back to Login Link -->
                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p class="text-muted">Back to <a href="{{ route('login') }}" class="text-muted ms-1"><b>Log In</b></a></p>
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->

                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end page -->

</x-guest-layout>