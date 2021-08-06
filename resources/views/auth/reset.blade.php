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
        <form class="card card-md" action="{{route('reset')}}" method="post">
            @csrf
            <div class="card-body">
                @if (\Session::has('error'))
                    <div class="alert alert-danger" style="margin:20px">
                        {{ \Session::get('error') }}
                    </div>
                @elseif (\Session::has('success'))
                    <div class="alert alert-success" style="margin:20px">
                        {{ \Session::get('success') }}
                    </div>
                @else
                    <h2 class="mb-5 text-center" style="margin-bottom:20px !important">Password Reset Form</h2>
                    <div class="mb-3">
                        @if ($errors->has('email'))
                            <label class="form-label text-red font-weight-bold">{{ $errors->first('email') }}</label>
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
                                <input type="email" name="email" class="form-control is-invalid"
                                       value="{{ old('email') }}" required tabindex="100">
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
                                <input type="email" name="email" class="form-control" required autofocus tabindex="100">
                            </div>
                        @endif
                    </div>
                    <div class="mb-2">
                        @if ($errors->has('password'))
                            <label class="form-label text-red font-weight-bold">{{ $errors->first('password') }}</label>
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
                                                                                 required tabindex="101"></div>
                            </div>
                        @else
                            <label class="form-label">New Password</label>
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
                                <input type="password" name="password" class="form-control" required tabindex="101">
                            </div>
                        @endif
                    </div>
                    <div class="mb-2">
                        @if ($errors->has('password_confirmation'))
                            <label
                                class="form-label text-red font-weight-bold">{{ $errors->first('password_confirmation') }}</label>
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
                                <div class="input-group input-group-flat"><input type="password"
                                                                                 name="password_confirmation"
                                                                                 class="form-control is-invalid"
                                                                                 required tabindex="101"></div>
                            </div>
                        @else
                            <label class="form-label">Confirm New Password</label>
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
                                <input type="password" name="password_confirmation" class="form-control" required
                                       tabindex="101">
                            </div>
                        @endif
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary btn-block" tabindex="103">Create reset link
                        </button>
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>
<script src="{{asset("libs/bootstrap/dist/js/bootstrap.bundle.min.js")}}"></script>
</body>
