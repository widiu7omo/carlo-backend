<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>{{env('APP_NAME')}} - Login</title>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <meta name="msapplication-TileColor" content="#206bc4"/>
    <meta name="theme-color" content="#206bc4"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="mobile-web-app-capable" content="yes"/>
    <meta name="HandheldFriendly" content="True"/>
    <meta name="MobileOptimized" content="320"/>
    <meta name="robots" content="noindex,nofollow,noarchive"/>
    <link rel="shortcut icon" href="img/favicon/favicon.ico"/>
    <link rel="apple-touch-icon" href="img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon/favicon-16x16.png">
    <link rel="manifest" href="img/favicon/site.webmanifest">
    <!-- CSS files -->
    <link href="{{asset("css/tabler.min.css")}}" rel="stylesheet"/>
    <link href="{{asset("css/demo.min.css")}}" rel="stylesheet"/>
</head>

<body class="antialiased border-top-wide border-primary d-flex flex-column">
<div class="flex-fill d-flex flex-column justify-content-center">
    <div class="container-tight py-6">
        <div class="text-center mb-4">
            <img src="./static/logo.svg" height="36" alt="">
        </div>
        <form class="card card-md" action="{{ route('login') }}" method="post">
            @csrf
            <input type="hidden" name="_token" value="{{ app('request')->session()->get('_token') }}">
            <div class="card-body">
                <h2 class="mb-5 text-center" style="margin-bottom:20px !important">Admin Panel Login</h2>
                <div class="mb-3">
                    @error('email')
                    <label class="form-label text-red font-weight-bold">{{ $message }}</label>
                    <div class="input-icon mb-3">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24"
                                     viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z"></path>
                                    <circle cx="12" cy="12" r="4"></circle>
                                    <path d="M16 12v1.5a2.5 2.5 0 0 0 5 0v-1.5a9 9 0 1 0 -5.5 8.28"></path>
                                </svg>
                            </span>
                        <input type="email" name="email" class="form-control is-invalid" value="{{ old('email') }}"
                               required autocomplete="email" autofocus tabindex="100">
                    </div>
                    @else
                        <label class="form-label">Email address</label>
                        <div class="input-icon mb-3">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24"
                                     viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z"></path>
                                    <circle cx="12" cy="12" r="4"></circle>
                                    <path d="M16 12v1.5a2.5 2.5 0 0 0 5 0v-1.5a9 9 0 1 0 -5.5 8.28"></path>
                                </svg>
                            </span>
                            <input type="email" name="email" class="form-control" placeholder="Enter email..." required
                                   autofocus tabindex="100">
                        </div>
                        @enderror
                </div>
                <div class="mb-2">
                    @error('password')
                    <label class="form-label text-red font-weight-bold">{{ $message }}<span
                            class="form-label-description text-blue font-weight-normal"><a href="./forgot-password.html"
                                                                                           tabindex="104">I forgot password</a></span></label>
                    <div class="input-icon mb-3">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24"
                                     viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z"></path>
                                    <circle cx="8" cy="15" r="4"></circle>
                                    <line x1="10.85" y1="12.15" x2="19" y2="4"></line>
                                    <line x1="18" y1="5" x2="20" y2="7"></line>
                                    <line x1="15" y1="8" x2="17" y2="10"></line>
                                </svg>
                            </span>
                        <div class="input-group input-group-flat"><input type="password" name="password"
                                                                         class="form-control is-invalid"
                                                                         placeholder="password" required
                                                                         autocomplete="current-password" tabindex="101">
                        </div>
                    </div>
                    @else
                        <label class="form-label">Password @if (Route::has('forget')) <span
                                class="form-label-description"><a href="{{ route('forget') }}" tabindex="104">I forgot password</a></span> @endif
                        </label>
                        <div class="input-icon mb-3">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24"
                                     viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z"></path>
                                    <circle cx="8" cy="15" r="4"></circle>
                                    <line x1="10.85" y1="12.15" x2="19" y2="4"></line>
                                    <line x1="18" y1="5" x2="20" y2="7"></line>
                                    <line x1="15" y1="8" x2="17" y2="10"></line>
                                </svg>
                            </span>
                            <input type="password" name="password" class="form-control" placeholder="password" required
                                   tabindex="101">
                        </div>
                        @enderror
                </div>
                <div class="mb-2">
                    <label class="form-check">
                        <input type="checkbox" name="remember" id="remember"
                               {{ old('remember') ? 'checked' : '' }} class="form-check-input" tabindex="102"/>
                        <span class="form-check-label">Remember me on this device</span>
                    </label>
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary btn-block" tabindex="103">Sign in</button>
                </div>
            </div>
            <!--
      <div class="card-body">
        <div class="btn-list">
        <span class="btn btn-outline-primary btn-block disabled">Email: demo@mintservice.ltd</span>
        <span class="btn btn-outline-success btn-block disabled">Password: demoaccess</span>
        </div>
      </div>
      -->
        </form>
    </div>
</div>
<!-- Libs JS -->
<script src="{{asset("libs/bootstrap/dist/js/bootstrap.bundle.min.js")}}"></script>
</body>

</html>
