<?php   
    if($_POST['kordat_hidden'] == 'Y') {  
        ///Form data sent  
        $dbapi = $_POST['kordat_dbapi'];  
        update_option('kordat_dbapi', $dbapi);  
          
        $dbproj = $_POST['kordat_dbproj'];  
        update_option('kordat_dbproj', $dbproj);  
        
        $dbscheme = $_POST['kordat_dbscheme'];  
        update_option('kordat_dbscheme', $dbscheme);  

        $dbtoken = $_POST['kordat_dbtoken'];  
        update_option('kordat_dbtoken', $dbtoken);  
        
        $dbuser = $_POST['kordat_dbuser'];  
        update_option('kordat_dbuser', $dbuser);  

        $dbpass = $_POST['kordat_dbpass'];  
        update_option('kordat_dbpass', $dbpass);
        
        ?>  
        <div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>  
        <?php  
    } else {  
		///Normal page display  
        $dbapi = get_option('kordat_dbapi');  
        $dbproj = get_option('kordat_dbproj');  
        $dbscheme = get_option('kordat_dbscheme');  
        $dbtoken = get_option('kordat_dbtoken');
        $dbuser = get_option('kordat_dbuser');  
        $dbpass = get_option('kordat_dbpass');
    }  
?>  

<div class="wrap">  
    <?php    echo "<h2>" . __( 'Kora General Settings', 'kordat_trdom' ) . "</h2>"; ?>  
      
    <form name="kordat_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">  
        <input type="hidden" name="kordat_hidden" value="Y">  
        <?php    echo "<h4>" . __( 'KORA Database Settings', 'kordat_trdom' ) . "</h4>"; ?>  
        <p><?php _e("URL of KORA Installation: " ); ?><input type="text" name="kordat_dbapi" value="<?php echo $dbapi; ?>" size="20"><?php _e(" <span class='setting_detail'>i.e. http://kora.example.org/</span>" ); ?></p>
        <p><?php _e("Project ID: " ); ?><input type="number" min="1" name="kordat_dbproj" value="<?php echo $dbproj; ?>"><?php _e(" <span class='setting_detail'>i.e. PID number</span>" ); ?></p>  
        <p><?php _e("Default Scheme ID: " ); ?><input type="number" min="1" name="kordat_dbscheme" value="<?php echo $dbscheme; ?>" size="10"><?php _e(" <span class='setting_detail'>i.e. SID number</span>" ); ?></p>  
        <p><?php _e("Token: " ); ?><input type="text" name="kordat_dbtoken" value="<?php echo $dbtoken; ?>" size="20"><?php _e(" <span class='setting_detail'>i.e. token granted access to project with PID above</span>" ); ?></p>  
        
        <hr>
        <?php    echo "<h4>" . __( 'Server Authentication Settings', 'kordat_trdom' ) . "</h4>"; ?>  
        <p><?php _e("Server Username: " ); ?><input type="text" name="kordat_dbuser" value="<?php echo $dbuser; ?>" size="20"><?php _e(" <span class='setting_detail'>Server credentials for accessing server-end API (Not your KORA database login</span>)" ); ?></p>  
        <p><?php _e("Server Password: " ); ?><input type="password" name="kordat_dbpass" value="<?php echo $dbpass; ?>" size="20"><?php _e(" <span class='setting_detail'>Server credentials for accessing server-end API</span>" ); ?></p>       
        <?php    echo __( '** These settings are only necessary if your Kora installation is protected by web-server authentication.  This is not the same as Kora management login information.', 'kordat_trdom' ); ?>  

        <hr>

        <p class="submit">  
        <input type="submit" name="Submit" value="<?php _e('Update Options', 'kordat_trdom' ) ?>" />  
        </p>  
    </form>  
</div>  