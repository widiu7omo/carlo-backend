<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>{{env('APP_NAME')}} - Panel</title>
    <meta name="msapplication-TileColor" content="#206bc4"/>
    <meta name="theme-color" content="#206bc4"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="mobile-web-app-capable" content="yes"/>
    <meta name="HandheldFriendly" content="True"/>
    <meta name="MobileOptimized" content="320"/>
    <meta name="robots" content="noindex,nofollow,noarchive"/>
    <link rel="shortcut icon" href="/img/favicon/favicon.ico"/>
    <link rel="apple-touch-icon" href="/img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon/favicon-16x16.png">
    <link rel="manifest" href="/img/favicon/site.webmanifest">
    <!-- CSS files -->
    <link href="{{asset("css/jqvmap/jqvmap.min.css")}}" rel="stylesheet"/>
    <link href="{{asset("css/tabler.min.css")}}" rel="stylesheet"/>
    <link href="{{asset("css/tablerd.min.css")}}" rel="stylesheet"/>
    @yield('css')
    @stack("style")
</head>

<body class="antialiased">
<div class="page">
    <header class="navbar navbar-expand-md navbar-dark">
        <div class="container-xl">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a href="/" class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pr-0 pr-md-3">
                <h2>{{env('APP_NAME')}}</h2>
            </a>
            <div class="navbar-nav flex-row order-md-last">
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-toggle="dropdown">
                        <span class="avatar"
                              style="background-image: url({{$admin_share['avatar'] == null ? '/public/img/admin.png' : $admin_share['avatar']}})"></span>
                        <div class="d-none d-xl-block pl-2">
                            <div>{{$admin_share['name']}}</div>
                            <div class="mt-1 small text-muted">Logged into Admin Panel</div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="{{route('admins')}}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24"
                                 height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"/>
                                <path
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            Profile settings
                        </a>
                        <a class="dropdown-item" onclick="resetTA()" href="#" data-toggle="modal"
                           data-target="#modal-note">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24"
                                 height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"/>
                                <path d="M9 7 h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3"/>
                                <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3"/>
                                <line x1="16" y1="5" x2="19" y2="8"/>
                            </svg>
                            Personal Note
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24"
                                 height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"></path>
                                <path d="M7 6a7.75 7.75 0 1 0 10 0"></path>
                                <line x1="12" y1="4" x2="12" y2="12"></line>
                            </svg>
                            {{ __('Logout') }}</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                              style="display: none;">@csrf</form>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="navbar-expand-md">
        <div class="collapse navbar-collapse" id="navbar-menu">
            <div class="navbar navbar-light">
                <div class="container-xl">
                    <ul class="navbar-nav">
                        <li class="nav-item active">
                            <a class="nav-link" href="{{route('index')}}">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                             viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z"/>
                                            <polyline points="5 12 3 12 12 3 21 12 19 12"/>
                                            <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/>
                                            <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"/></svg>
                                    </span>
                                <span class="nav-link-title">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#navbar-base" data-toggle="dropdown" role="button"
                               aria-expanded="false">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24"
                                             height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                             fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                            <path d="M5.5 21v-2a4 4 0 0 1 4 -4h5a4 4 0 0 1 4 4v2"></path>
                                        </svg>
                                    </span>
                                <span class="nav-link-title">User Management</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{route('members')}}">Users Directory</a></li>
                                <li><a class="dropdown-item" href="{{route('bannedmembers')}}">Banned Users</a></li>
                                <div class="dropdown-divider"></div>
                                <li><a class="dropdown-item" href="{{route('history')}}">Activities History</a></li>
                                <li><a class="dropdown-item" href="{{route('withdraw')}}">Withdrawal Activity</a></li>
                                <div class="dropdown-divider"></div>
                                <li><a class="dropdown-item" href="{{route('push_msg')}}">Send Push Message</a></li>
                                <li><a class="dropdown-item" href="{{route('local_msg')}}">Send Local Message</a></li>
                                <div class="dropdown-divider"></div>
                                <li><a class="dropdown-item font-weight-bold" href="{{route('support')}}">User
                                        Support</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#navbar-base" data-toggle="dropdown" role="button"
                               aria-expanded="false">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24"
                                             height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                             fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z"></path>
                                            <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                                            <path d="M7 15v-4a2 2 0 0 1 4 0v4"></path>
                                            <line x1="7" y1="13" x2="11" y2="13"></line>
                                            <path d="M17 9v6h-1.5a1.5 1.5 0 1 1 1.5 -1.5"></path>
                                        </svg>
                                    </span>
                                <span class="nav-link-title">Offerwalls</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{route('networks_cpa')}}">API Offerwalls</a></li>
                                <li><a class="dropdown-item" href="{{route('networks_sdk')}}">SDK Offerwalls</a></li>
                                <li><a class="dropdown-item" href="{{route('networks_web')}}">Web Offerwalls</a></li>
                                <li><a class="dropdown-item" href="{{route('networks_cpv')}}">Video Ads</a></li>
                                <div class="dropdown-divider"></div>
                                <li><a class="dropdown-item" href="{{route('networks_custom')}}">Custom Offerwall</a>
                                </li>
                                <li><a class="dropdown-item" href="{{route('networks_youtube')}}">YouTube Videos</a>
                                </li>
                                <li><a class="dropdown-item text-muted" href="{{route('networks_ppv')}}">Pay Per View
                                        &nbsp; <small>(disabled)</small></a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#navbar-base" data-toggle="dropdown" role="button"
                               aria-expanded="false">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24"
                                             height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                             fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z"/>
                                            <rect x="2" y="6" width="20" height="12" rx="2"/>
                                            <path d="M6 12h4m-2 -2v4"/>
                                            <line x1="15" y1="11" x2="15" y2="11.01"/>
                                            <line x1="18" y1="13" x2="18" y2="13.01"/></svg>
                                    </span>
                                <span class="nav-link-title">Games</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item font-weight-bold" href="{{route('game_tour')}}">Quiz
                                        Tournament</a></li>
                                <div class="dropdown-divider"></div>
                                <li><a class="dropdown-item" href="{{route('game_quiz_category')}}">Quiz Game</a></li>
                                <li><a class="dropdown-item" href="{{route('game_guess_word')}}">Word Guessing</a></li>
                                <li><a class="dropdown-item" href="{{route('game_ip_category')}}">Image Puzzle</a></li>
                                <li><a class="dropdown-item" href="{{route('game_jpz_category')}}">Jigsaw Puzzle</a>
                                </li>
                                <li><a class="dropdown-item" href="{{route('game_wheel')}}">Spin Wheel</a></li>
                                <li><a class="dropdown-item" href="{{route('game_slot')}}">Slot Game</a></li>
                                <li><a class="dropdown-item" href="{{route('game_lotto')}}">Lotto Game</a></li>
                                <li><a class="dropdown-item" href="{{route('game_scratch_cat')}}">Scratcher Game</a>
                                </li>
                                <div class="dropdown-divider"></div>
                                <li><a class="dropdown-item" href="{{route('game_html')}}">HTML5 Games</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#navbar-base" data-toggle="dropdown" role="button"
                               aria-expanded="false">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24"
                                             height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                             fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z"/>
                                            <path
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <circle cx="12" cy="12" r="3"/></svg>
                                    </span>
                                <span class="nav-link-title">Setup</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{route('gateway_category')}}">Withdrawal Setup</a>
                                </li>
                                <li><a class="dropdown-item" href="{{route('activity_reward')}}">Activity Reward
                                        Setup</a></li>
                                <div class="dropdown-divider"></div>
                                <li><a class="dropdown-item" href="{{route('frauds')}}">Fraud Prevention</a></li>
                                <li><a class="dropdown-item" href="{{route('geo_api')}}">Geo API Setup</a></li>
                                <div class="dropdown-divider"></div>
                                <li><a class="dropdown-item" href="{{route('settings')}}">System Settings</a></li>
                                <li><a class="dropdown-item" href="{{route('faq_admin')}}">FAQ Management</a></li>
                                <li><a class="dropdown-item" href="{{route('maintain')}}">Maintenance</a></li>
                            </ul>
                        </li>
                    </ul>
                    <div class="my-2 my-md-0 flex-grow-1 flex-md-grow-0 order-first order-md-last">
                        <form action="{{route('membersearch')}}" method="post">
                            @csrf
                            <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                             viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z"/>
                                            <circle cx="10" cy="10" r="7"/>
                                            <line x1="21" y1="21" x2="15" y2="15"/></svg>
                                    </span>
                                <input type="text" name="search" class="form-control" placeholder="Search users…">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-xl">
            @yield('content')
        </div>
        <footer class="footer footer-transparent">
            <div class="container">
                <div class="row text-center align-items-center flex-row-reverse">
                    <div class="col-lg-auto ml-lg-auto">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item"><a href="{{route('faq')}}" target="_blank"
                                                            class="link-secondary">FAQ</a></li>
                            <li class="list-inline-item"><a href="{{route('terms')}}" target="_blank"
                                                            class="link-secondary">Terms &amp; Conditions</a></li>
                            <li class="list-inline-item"><a href="{{route('privacy')}}" target="_blank"
                                                            class="link-secondary">Privacy Policy</a></li>
                        </ul>
                    </div>
                    <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                        Copyright © {{date("Y")}}
                        <a href="{{env('APP_URL')}}" class="link-secondary">{{env('APP_NAME')}}</a>.
                        All rights reserved.
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>
<form method="post" action="{{route('save_admin_note')}}" class="modal modal-blur fade" id="modal-note" tabindex="-1"
      role="dialog" aria-hidden="true">
    @csrf
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Admin note:</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"></path>
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="modal-body pt-1">
                <textarea name="a_note" class="form-control" data-toggle="autosize"
                          placeholder="Write down your note here...">{!!$admin_share['note']!!}</textarea>
                <button type="submit" class="btn btn-block btn-dark mt-3">Save and close</button>
            </div>
        </div>
    </div>
</form>
<script src="{{asset("js/bootstrap.bundle.min.js")}}"></script>
<script src="{{asset("js/autosize.min.js")}}"></script>
<script>
    function resetTA() {
        setTimeout(function () {
            const elements = document.querySelectorAll('[data-toggle="autosize"]');
            if (elements.length) {
                elements.forEach(function (element) {
                    autosize(element);
                });
            }
        }, 200);
    };

</script>
@yield('javascript')
@stack("js")
</body>

</html>
