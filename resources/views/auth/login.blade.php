<!doctype html>
<html lang="en">

    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('admin/fonts/icomoon/style.css') }}">
        <link rel="stylesheet" href="{{ asset('admin/css/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('admin/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}">

        <title>Login</title>
    </head>

    <body>

        <div class="d-lg-flex half">
            <!-- Background -->
            <div class="bg order-1 order-md-2" style="background-image: url('{{ asset('admin/images/bg_1.jpg') }}');">
            </div>

            <!-- Content -->
            <div class="contents order-2 order-md-1">
                <div class="container">
                    <div class="row align-items-center justify-content-center">
                        <div class="col-md-7">
                            <h3>Login to <strong>Colorlib</strong></h3>
                            <p class="mb-4">Lorem ipsum dolor sit amet elit. Sapiente sit aut eos consectetur
                                adipisicing.</p>

                            <!-- Laravel Login Form -->
                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <!-- Email -->
                                <div class="form-group first">
                                    <label for="email">{{ __('Email Address') }}</label>
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" placeholder="your-email@gmail.com" required
                                        autocomplete="email" autofocus>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div class="form-group last mb-3">
                                    <label for="password">{{ __('Password') }}</label>
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        placeholder="Your Password" required autocomplete="current-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- Remember Me + Forgot Password -->
                                <div class="d-flex mb-5 align-items-center">
                                    <label class="control control--checkbox mb-0">
                                        <span class="caption">{{ __('Remember Me') }}</span>
                                        <input type="checkbox" name="remember" id="remember"
                                            {{ old('remember') ? 'checked' : '' }}>
                                        <div class="control__indicator"></div>
                                    </label>

                                    @if (Route::has('password.request'))
                                        <span class="ml-auto">
                                            <a href="{{ route('password.request') }}" class="forgot-pass">
                                                {{ __('Forgot Your Password?') }}
                                            </a>
                                        </span>
                                    @endif
                                </div>

                                <!-- Submit -->
                                <input type="submit" value="{{ __('Login') }}" class="btn btn-block btn-primary">
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <script src="{{ asset('admin/js/jquery-3.3.1.min.js') }}"></script>
        <script src="{{ asset('admin/js/popper.min.js') }}"></script>
        <script src="{{ asset('admin/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('admin/js/main.js') }}"></script>

    </body>

</html>
