<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Logo -->
    <li class="sidebar-logo-container">
        <a href="<?php echo e(route('admin.users.index')); ?>" class="sidebar-logo-link">
            <img src="<?php echo e(asset('assets/index_files/logo.png')); ?>" alt="Go Trips" class="sidebar-logo">
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider my-3">

    <!-- Navigation Items -->
    <div class="sidebar-nav-items px-3">

        <?php if(auth()->user()->isAdmin()): ?>
        <!-- Supervisor (Admin only) -->
        <li class="nav-item mb-1">
            <a class="nav-link d-flex align-items-center py-3 px-3 rounded <?php echo e(request()->is('admin/users') || request()->is('admin/users/*') ? 'active' : ''); ?>"
               href="<?php echo e(route('admin.users.index')); ?>">
                <i class="fas fa-desktop me-3 nav-icon"></i>
                <span><?php echo e(__('Supervisor')); ?></span>
            </a>
        </li>
        <?php endif; ?>

        <?php if(auth()->user()->hasPermission('announcement_access')): ?>
        <!-- Announcements -->
        <li class="nav-item mb-1">
            <a class="nav-link d-flex align-items-center py-3 px-3 rounded <?php echo e(request()->routeIs('admin.announcements.*') ? 'active' : ''); ?>"
               href="<?php echo e(route('admin.announcements.index')); ?>">
                <i class="fas fa-bullhorn me-3 nav-icon"></i>
                <span>Announcements</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if(auth()->user()->hasPermission('homepage_ads_access')): ?>
        <!-- Carousel Images -->
        <li class="nav-item mb-1">
            <a class="nav-link d-flex align-items-center py-3 px-3 rounded <?php echo e(request()->routeIs('admin.homepageads.*') ? 'active' : ''); ?>"
               href="<?php echo e(route('admin.homepageads.index')); ?>">
                <i class="fas fa-images me-3 nav-icon"></i>
                <span>Carousel Images</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if(auth()->user()->hasPermission('activity_access')): ?>
        <!-- UAE Activities -->
        <li class="nav-item mb-1">
            <a class="nav-link d-flex align-items-center py-3 px-3 rounded <?php echo e(request()->routeIs('admin.uaeactivities.*') ? 'active' : ''); ?>"
               href="<?php echo e(route('admin.uaeactivities.index')); ?>">
                <i class="fas fa-map-marked-alt me-3 nav-icon"></i>
                <span>UAE Activities</span>
            </a>
        </li>
        <?php endif; ?>

    </div>

    <!-- Bottom Divider -->
    <hr class="sidebar-divider mt-4">

</ul>
<?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/partials/sidebar.blade.php ENDPATH**/ ?>