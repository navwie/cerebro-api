<div class="c-sidebar-brand">
    <img class="cerebro-logo-sidebar" src="{{asset('assets/brand/cerebroLogo.svg')}}" alt="">
</div>
<ul class="c-sidebar-nav">
    <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link" href="{{ url('home') }}">
            <i class="cil-speedometer c-sidebar-nav-icon"></i>
            Loan dashboard
        </a>
    </li>
    <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link" href="{{ url('cards') }}">
            <i class="cil-credit-card c-sidebar-nav-icon"></i>
            Cards dashboard
        </a>
    </li>
    <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link" href="{{ url('statistic') }}">
            <i class="cil-chart c-sidebar-nav-icon"></i>
            Statistic
        </a>
    </li>
    @if(auth()->user()->hasRole('admin'))
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ url('forms') }}">
                <i class="cil-description c-sidebar-nav-icon"></i>
                Forms
            </a>
        </li>

        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ url('sites') }}">
                <i class="cil-sitemap c-sidebar-nav-icon"></i>
                Sites
            </a>
        </li>

        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ url('servers') }}">
                <i class="cil-settings c-sidebar-nav-icon"></i>
                Servers
            </a>
        </li>
    @else
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ url('profile') }}">
                <i class="cil-user c-sidebar-nav-icon"></i>
                Profile
            </a>
        </li>
    @endif
</ul>
<button class="c-sidebar-minimizer c-class-toggler" type="button" data-target="_parent"
        data-class="c-sidebar-minimized"></button>
</div>
