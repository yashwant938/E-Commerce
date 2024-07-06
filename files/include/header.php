<?php
if(!defined('app')) {
   die('Direct access not permitted');
}
?>

<header class="p-3 border-bottom">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
        <a href="<?php echo $route->urlFor('home'); ?>" class="d-flex align-items-center mb-2 mb-lg-0 link-body-emphasis text-decoration-none">
          <h3>Cyb3rC1ph3r</h3>
        </a>

        <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
        </ul>

        <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search">
          
        </form>

        <div class="dropdown text-end">
          <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fa-solid fa-circle-user fa-2xl"></i>
          </a>
          <ul class="dropdown-menu text-small">
            <li><a class="dropdown-item" href="<?php echo $route->urlFor('myPropertyList'); ?>">My Property</a></li>
            <li><a class="dropdown-item" href="<?php echo $route->urlFor('checkPropertyForm'); ?>">Verify Document</a></li>
            <li><a class="dropdown-item" href="<?php echo $route->urlFor('profile'); ?>">Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="<?php echo $route->urlFor('logout'); ?>">Sign out</a></li>
          </ul>
        </div>
      </div>
    </div>
  </header>