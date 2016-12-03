<?php
//for 
if ( ! defined( 'ABSPATH' ) ) exit;
$page=sanitize_text_field($_GET['page']);
?>
<div class="options-area">
<aside class="sidebar pull-left">
    <div class="wpdevart-tabs">
        <ul class="nav nav-tabs"> 
          <li <?php if($page==$this->slug['list']) echo "class='active'";?>>
         	 <a href='<?php echo admin_url("admin.php?page=".$this->slug['list']); ?>'>
             	Forms
             </a>
          </li>
          
         <?php if($page== $this->slug['submissions']) { ?>
         <li <?php if($page==$this->slug['submissions']) echo "class='active'";?>>
          <a href='<?php echo admin_url ("admin.php?page=".$this->slug['submissions']); ?>'>
         	 Submissions
          </a>
         </li>
         <?php } ?>
         
          <li <?php if($page==$this->slug['add_new']) echo "class='active'";?>>
           	<a  href='<?php echo admin_url  ("admin.php?page=".$this->slug['add_new']); ?>'>
            	Add New
            </a>
          </li>
          
          <?php if($page == $this->slug['edit']) { ?>
          <li <?php if($page == $this->slug['edit']) echo "class='active'";?>>
           	<a  href='<?php echo admin_url  ("admin.php?page=".$this->slug['edit']); ?>'>
            	Edit
            </a>
          </li>
          <?php } ?>
          
          <li <?php if($page == $this->slug['styling']) echo "class='active'";?>>
          <a href='<?php echo admin_url ("admin.php?page=".$this->slug['styling']); ?>'>
         		Styling<span class="wpdevart_pro_span"> (PRO)</span>
          </a>
         </li>
         
         <?php if($page == $this->slug['settings']) { ?>
         <li <?php if($page == $this->slug['settings']) echo "class='active'";?>>
          <a href='<?php echo admin_url ("admin.php?page=".$this->slug['settings']); ?>'>
         		Settings
          </a>
         </li>
         <?php } ?>
         
          
         <!--<li <?php if($page == $this->slug['extra_settings']) echo "class='active'";?>>
          <a href='<?php echo admin_url ("admin.php?page=".$this->slug['extra_settings']); ?>'>
         		Extra Settings
          </a>
         </li>
         -->
          
        </ul>
    </div>
</aside><!-- / wpdevart-sidebar -->