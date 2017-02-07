<?php
$target_url = 'http://192.168.56.1/php-practice/sync/vmn/rdiff_make_patch.php';
//	$target_url = 'https://moodle-experiment-fajarpurnama.c9users.io/upload/receive.php';
        //This needs to be the full path to the file you want to send.
	$file_name_with_full_path = realpath('../sync/backup.mbz.delta');
        /* curl will accept an array here too.
         * Many examples I found showed a url-encoded string instead.
         * Take note that the 'key' in the array will be the key that shows up in the
         * $_FILES array of the accept script. and the at sign '@' is required before the
         * file name.
         */
	$post = array('extra_info' => '123456','file_contents'=>new \CURLFile($file_name_with_full_path));
 
        $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$target_url);
	curl_setopt($ch, CURLOPT_POST,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	$result=curl_exec ($ch);
	curl_close ($ch);
	echo $result;
?>
