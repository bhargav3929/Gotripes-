<ul class="navbar-nav sidebar sidebar-dark accordion" 
    style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #1a1a1a 100%); border-right: 3px solid #FFD700;" 
    id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center py-4 mb-3" 
       href="{{ url('/') }}" 
       style="background: linear-gradient(45deg, #FFD700, #FFA500); color: #000; text-decoration: none; border-radius: 8px; margin: 15px;">
        <div class="sidebar-brand-text mx-3 fw-bold fs-5">
            <i class="fas fa-home me-2"></i>{{ __('Homepage') }}
        </div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-3" style="border-color: #FFD700; opacity: 0.6;">

    <!-- Navigation Items -->
    <div class="px-3">
        
        <!-- Supervisor -->
        <li class="nav-item mb-2 {{ request()->is('admin/users') || request()->is('admin/users') ? 'active' : '' }}">
            <a class="nav-link d-flex align-items-center py-3 px-3 rounded" 
               href="{{ route('admin.users.index') }}"
               style="color: #FFD700; transition: all 0.3s ease; {{ request()->is('admin/users') ? 'background: linear-gradient(45deg, #FFD700, #FFA500); color: #000; font-weight: bold;' : '' }}"
               onmouseover="this.style.background='rgba(255, 215, 0, 0.1)'; this.style.transform='translateX(5px)';"
               onmouseout="this.style.background='{{ request()->is('admin/users') ? 'linear-gradient(45deg, #FFD700, #FFA500)' : 'transparent' }}'; this.style.transform='translateX(0)';">
                <i class="fas fa-desktop me-3" style="width: 20px; text-align: center;"></i>
                <span>{{ __('Supervisor') }}</span>
            </a>
        </li>

        <!-- Announcements -->
        <li class="nav-item mb-2">
            <a class="nav-link d-flex align-items-center py-3 px-3 rounded {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}" 
               href="{{ route('admin.announcements.index') }}"
               style="color: #FFD700; transition: all 0.3s ease; {{ request()->routeIs('admin.announcements.*') ? 'background: linear-gradient(45deg, #FFD700, #FFA500); color: #000; font-weight: bold;' : '' }}"
               onmouseover="this.style.background='rgba(255, 215, 0, 0.1)'; this.style.transform='translateX(5px)';"
               onmouseout="this.style.background='{{ request()->routeIs('admin.announcements.*') ? 'linear-gradient(45deg, #FFD700, #FFA500)' : 'transparent' }}'; this.style.transform='translateX(0)';">
                <i class="fas fa-bullhorn me-3" style="width: 20px; text-align: center;"></i>
                <span>Announcements</span>
            </a>
        </li>

        <!-- Carousel Images -->
        <li class="nav-item mb-2">
            <a class="nav-link d-flex align-items-center py-3 px-3 rounded {{ request()->routeIs('admin.homepageads.*') ? 'active' : '' }}" 
               href="{{ route('admin.homepageads.index') }}"
               style="color: #FFD700; transition: all 0.3s ease; {{ request()->routeIs('admin.homepageads.*') ? 'background: linear-gradient(45deg, #FFD700, #FFA500); color: #000; font-weight: bold;' : '' }}"
               onmouseover="this.style.background='rgba(255, 215, 0, 0.1)'; this.style.transform='translateX(5px)';"
               onmouseout="this.style.background='{{ request()->routeIs('admin.homepageads.*') ? 'linear-gradient(45deg, #FFD700, #FFA500)' : 'transparent' }}'; this.style.transform='translateX(0)';">
                <i class="fas fa-images me-3" style="width: 20px; text-align: center;"></i>
                <span>Carousel Images</span>
            </a>
        </li>

        <!-- UAE Activities -->
        <li class="nav-item mb-2">
            <a class="nav-link d-flex align-items-center py-3 px-3 rounded {{ request()->routeIs('admin.uaeactivities.*') ? 'active' : '' }}" 
               href="{{ route('admin.uaeactivities.index') }}"
               style="color: #FFD700; transition: all 0.3s ease; {{ request()->routeIs('admin.uaeactivities.*') ? 'background: linear-gradient(45deg, #FFD700, #FFA500); color: #000; font-weight: bold;' : '' }}"
               onmouseover="this.style.background='rgba(255, 215, 0, 0.1)'; this.style.transform='translateX(5px)';"
               onmouseout="this.style.background='{{ request()->routeIs('admin.uaeactivities.*') ? 'linear-gradient(45deg, #FFD700, #FFA500)' : 'transparent' }}'; this.style.transform='translateX(0)';">
                <i class="fas fa-map-marked-alt me-3" style="width: 20px; text-align: center;"></i>
                <span>UAE Activities</span>
            </a>
        </li>

    </div>

    <!-- Bottom Divider -->
    <hr class="sidebar-divider mt-4" style="border-color: #FFD700; opacity: 0.3;">

</ul>

<!-- Optional: Add this CSS for additional styling -->
<style>
.sidebar {
    box-shadow: 2px 0 10px rgba(255, 215, 0, 0.1);
    min-height: 100vh;
}

.sidebar .nav-link:hover {
    box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
}

.sidebar .nav-link.active {
    box-shadow: 0 4px 12px rgba(255, 215, 0, 0.5);
}

.sidebar-brand:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.4);
}

@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }
    
    .sidebar.show {
        transform: translateX(0);
    }
}
</style>
