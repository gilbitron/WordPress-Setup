<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
    <title>WordPress Setup</title> 
    <style type="text/css">
    html{background:#f9f9f9;}body{background:#fff;color:#333;font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;margin:2em auto;width:700px;padding:1em 2em;-moz-border-radius:11px;-khtml-border-radius:11px;-webkit-border-radius:11px;border-radius:11px;border:1px solid #dfdfdf;}a{color:#2583ad;text-decoration:none;}a:hover{color:#d54e21;}h1{border-bottom:1px solid #dadada;clear:both;color:#666;font:24px Georgia,"Times New Roman",Times,serif;margin:5px 0 0 -4px;padding:0;padding-bottom:7px;}h2{font-size:16px;}p,li,dd,dt{padding-bottom:2px;font-size:12px;line-height:18px;}code,.code{font-size:13px;}ul,ol,dl{padding:5px 5px 5px 22px;}a img{border:0;}abbr{border:0;font-variant:normal;}#logo{margin:6px 0 14px 0;border-bottom:none;text-align:center;}.step{margin:20px 0 15px;}.step,th{text-align:left;padding:0;}.submit input,.button,.button-secondary{font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;text-decoration:none;font-size:14px!important;line-height:16px;padding:6px 12px;cursor:pointer;border:1px solid #bbb;color:#464646;-moz-border-radius:15px;-khtml-border-radius:15px;-webkit-border-radius:15px;border-radius:15px;-moz-box-sizing:content-box;-webkit-box-sizing:content-box;-khtml-box-sizing:content-box;box-sizing:content-box;}.button:hover,.button-secondary:hover,.submit input:hover{color:#000;border-color:#666;}.button,.submit input,.button-secondary{background:#f2f2f2 url(../images/white-grad.png) repeat-x scroll left top;}.button:active,.submit input:active,.button-secondary:active{background:#eee url(../images/white-grad-active.png) repeat-x scroll left top;}textarea{border:1px solid #bbb;-moz-border-radius:4px;-khtml-border-radius:4px;-webkit-border-radius:4px;border-radius:4px;}.form-table{border-collapse:collapse;margin-top:1em;width:100%;}.form-table td{margin-bottom:9px;padding:10px;border-bottom:8px solid #fff;font-size:12px;}.form-table th{font-size:13px;text-align:left;padding:16px 10px 10px 10px;border-bottom:8px solid #fff;width:130px;vertical-align:top;}.form-table tr{background:#f3f3f3;}.form-table code{line-height:18px;font-size:18px;}.form-table p{margin:4px 0 0 0;font-size:11px;}.form-table input{line-height:20px;font-size:15px;padding:2px;}.form-table th p{font-weight:normal;}#error-page{margin-top:50px;}#error-page p{font-size:12px;line-height:18px;margin:25px 0 20px;}#error-page code,.code{font-family:Consolas,Monaco,Courier,monospace;}#pass-strength-result{background-color:#eee;border-color:#ddd!important;border-style:solid;border-width:1px;margin:5px 5px 5px 1px;padding:5px;text-align:center;width:200px;display:none;}#pass-strength-result.bad{background-color:#ffb78c;border-color:#ff853c!important;}#pass-strength-result.good{background-color:#ffec8b;border-color:#fc0!important;}#pass-strength-result.short{background-color:#ffa0a0;border-color:#f04040!important;}#pass-strength-result.strong{background-color:#c3ff88;border-color:#8dff1c!important;}.message{border:1px solid #e6db55;padding:.3em .6em;margin:5px 0 15px;background-color:#ffffe0;}
    .error{border:1px solid #e6db55;padding:.3em .6em;margin:5px 0 15px;background-color:#ffffe0;-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;}
    h1{margin-bottom:1em;}#footer{margin-top:1em;text-align:center;color:#999;}#footer p{margin:0;}
    </style>
</head> 
<body>
<h1>WordPress Setup</h1>
<?php 

function comma_list_ended($arr,$last = 'and') {
    return preg_replace("/,([^,]*)$/", " {$last} $1", join($arr,', '));
}

function slug($str)
{
	$str = strtolower(trim($str));
	$str = preg_replace('/[^a-z0-9-]/', '_', $str);
	$str = preg_replace('/-+/', "_", $str);
	return $str;
}

$running = true;

if(!empty($_POST)){
    
    $error = array();
    if($_POST['installation_name'] == ''){ 
        $error[] = 'an Installation Name';
        $running = false;
    }
    
    if($_POST['db_host'] == ''){ 
        $error[] = 'a Database Host';
        $running = false;
    }
    
    if($_POST['db_user'] == ''){ 
        $error[] = 'a Database User';
        $running = false;
    }
    
    if($_POST['db_password'] == ''){ 
        $error[] = 'a Database Password';
        $running = false;
    }
    
    if($running){
        $install_name = slug($_POST['installation_name']);
        $db_host = $_POST['db_host'];
        $db_user = $_POST['db_user'];
        $db_pass = $_POST['db_password'];
        
        set_time_limit(60); // To avoid timeouts increase this

        $path_parts = pathinfo(__FILE__);
        $base_dir = $path_parts['dirname'];

        if(is_dir($base_dir .'/'. $install_name)){
            die('<p class="error"><strong>Error:</strong> The installation directory <code>'. $base_dir .'/'. $install_name .'</code> already exists.</p><p><a href="wp-setup.php">&laquo; Back</a></p>');
        }
        if(is_dir($base_dir .'/wordpress') && (!isset($_POST['overwrite']) || $_POST['overwrite'] != true)){
            die('<p class="error"><strong>Error:</strong> The directory <code>'. $base_dir .'/wordpress</code> already exists.</p>'.
                '<p>This directory will be overwritten. Are you sure you want to continue?'.
                '<form action="" method="post">
                <input type="hidden" name="installation_name" value="'. $_POST['installation_name'] .'" />
                <input type="hidden" name="db_host" value="'. $_POST['db_host'] .'" />
                <input type="hidden" name="db_user" value="'. $_POST['db_user'] .'" />
                <input type="hidden" name="db_password" value="'. $_POST['db_password'] .'" />
                <input type="hidden" name="overwrite" value="true" />
                <p class="step"><input name="submit" type="submit" value="Continue" class="button"></p></form>');
        } else {
            if(!isset($_POST['overwrite']) || $_POST['overwrite'] != true){
                if(!mkdir($base_dir .'/wordpress', 0777)){
                    die('<p class="error"><strong>Error:</strong> Failed to create the directory <code>'. $base_dir .'/wordpress</code>. Please create this manually.</p>');
                }
            }
        }

        echo '<p>Downloading the latest version of WordPress...</p>';
        $zip_file = $base_dir .'/'. $install_name .'.zip';

        if(function_exists('curl_init')){
            $fp = fopen($zip_file, 'w');
            $ch = curl_init('http://wordpress.org/latest.zip');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            $data = curl_exec($ch);
            curl_close($ch);
            fclose($fp);
            
            if($data === false){
                die('<p class="error"><strong>Error:</strong> Failed to download the latest version of WordPress.</p>');
            }
        } else {
            $data = @file_get_contents('http://wordpress.org/latest.zip');
            if($data === false){
                die('<p class="error"><strong>Error:</strong> Failed to download the latest version of WordPress.</p>');
            } else {
                file_put_contents($zip_file, $data);
            }
        }

        echo '<p>Unzipping archive...</p>';
        $zip = new ZipArchive;
        $res = $zip->open($zip_file);
        if($res === TRUE){
            $zip->extractTo($base_dir);
            $zip->close();
            unlink($zip_file);
        } else {
            echo '<p class="error"><strong>Error:</strong> Failed to unzip the archive <code>'. $zip_file .'</code>. Please unzip manually.</p>';
        }

        if(!@rename($base_dir .'/wordpress', $base_dir .'/'. $install_name)){
            echo '<p class="error"><strong>Error:</strong> Failed to rename <code>'. $base_dir .'/wordpress</code> to <code>'. $base_dir .'/'. $install_name .'</code>. Please do this manually.</p>';
        }

        echo '<p>Creating database...</p>';
        $con = @mysql_connect($db_host, $db_user, $db_pass);
        if(!$con){
            die('<p class="error"><strong>Error:</strong> '. mysql_error() .'</p>');
        }
        if(!mysql_query('CREATE DATABASE '. $install_name, $con)){
            echo '<p class="error"><strong>Error:</strong> '. mysql_error() .'</p>';
        }
        mysql_close($con);

        echo '<p><strong>Success!</strong> <a href="'. $install_name .'">Continue &raquo;</a></p>';
    } else {
        echo '<p class="error"><strong>Error:</strong> You must include '. comma_list_ended($error) .'</p>';
    }

} else {
    $running = false;
}

if(!$running){
?>
<form action="" method="post">
    <table class="form-table">
		<tbody><tr>
			<th scope="row"><label for="installation_name">Installation Name</label></th>
			<td><input type="text" id="installation_name" name="installation_name" size="25" /></td>
			<td>Lowercase and underscores please. Used for site slug and database name (e.g. "test_wp" would create http://localhost/test_wp etc.)</td>
		</tr>
		<tr>
			<th scope="row"><label for="db_host">Database Host</label></th>
			<td><input type="text" id="db_host" name="db_host" value="localhost" size="25" /></td>
			<td>Will be "localhost" most of the time</td>
		</tr>
		<tr>
			<th scope="row"><label for="db_user">Database User</label></th>
			<td><input type="text" id="db_user" name="db_user" size="25" /></td>
			<td>Must be a user with "CREATE DATABASE" privileges (probably root)</td>
		</tr>
		<tr>
			<th scope="row"><label for="db_password">Database Password</label></th>
			<td><input type="password" id="db_password" name="db_password" size="25" /></td>
			<td></td>
		</tr>
	</tbody></table>
    <p class="step"><input name="submit" type="submit" value="Submit" class="button"></p>
</form>
<?php } ?>
<div id="footer">
    <p>Released under the MIT license by <a href="http://gilbert.pellegrom.me/">Gilbert Pellegrom</a> from <a href="http://dev7studios.com/">Dev7studios</a></p>
</div>
</body>
</html>