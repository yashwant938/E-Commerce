<?php
if(!defined('app')) {
   die('Direct access not permitted');
}
?>
<ul id="slide-out" class="sidenav">
    <li><div class="user-view">
      <div class="background">
        <img src="https://images.unsplash.com/photo-1696187488666-5d45f5aa384b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80">
      </div>
      <div class="nav-blur"></div>
      <div class="dp circle">
        <?php
                                echo '<img src="">';
                            ?>
    </div>
      <span class="white-text name">Sonik</span>
      <span class="white-text level">Admin</span>
    </div></li>
    
    <li><a class="waves-effect" href="../user/index.php">Users</a></li>
    <li><a class="waves-effect" href="../property/index.php">Property</a></li>

    <li><a class="waves-effect" href="../user/logout.php">Logout</a></li>
  </ul>
