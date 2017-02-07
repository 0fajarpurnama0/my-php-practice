<?php
 class backup {
   // Creating some properties (variables tied to an object)
            public $backup_dir;

   // Assigning the values
            public function __construct($backup_dir, $method, $url) {
              $this->backup_dir = $backup_dir;
	      $this->url = $url;
              $this->method = $method;
            }   
  
   // Creating a method (function tied to an object)
            public function make_signature() {
              if (file_exists($this->backup_dir.'/backup.mbz')) {
                if (file_exists($this->backup_dir.'/backup.mbz.sig')) {
		  unlink($this->backup_dir.'/backup.mbz.sig');
	        }
                if (strcmp($this->method,'rdiffdir') == 0){
                  exec("rm -r $this->backup_dir/backup");
                  mkdir($this->backup_dir.'/backup');
                  exec("tar xf $this->backup_dir/backup.mbz -C $this->backup_dir/backup; chmod -R 777 $this->backup_dir/backup; rdiffdir signature $this->backup_dir/backup $this->backup_dir/backup.mbz.sig");
                } else {
	          exec("rdiff signature $this->backup_dir/backup.mbz $this->backup_dir/backup.mbz.sig");
                }
	      }
            }

	    public function send_signature() {
              if (file_exists($this->backup_dir.'/backup.mbz.sig')) {
	        $target_url = $this->url.'rdiff_make_patch.php';
	        $file_name_with_full_path = realpath($this->backup_dir.'/backup.mbz.sig');
 	        $post = array('extra_info' => '123456','file_contents'=>new \CURLFile($file_name_with_full_path), 'method' => $this->method);
	        $ch = curl_init();
 	        curl_setopt($ch, CURLOPT_URL,$target_url);
 	        curl_setopt($ch, CURLOPT_POST,1);
 	        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
 	        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
 	        $result=curl_exec ($ch);
 	        curl_close ($ch);
 	        echo $result;
              } else {
                $url='local_apply_patch.php';
                echo '<META HTTP-EQUIV=REFRESH CONTENT="1; '.$url.'">';
              }
	    }
	    
	    public function receive_signature() {
	      if (file_exists('backup.mbz.sig')) {
		unlink('backup.mbz.sig');
	      }
              $uploaddir = realpath('./').'/';
              $uploadfile = $uploaddir.basename($_FILES['file_contents']['name']);
              echo '<pre>';
	      if (move_uploaded_file($_FILES['file_contents']['tmp_name'], $uploadfile)) {
	        echo 'File is valid, and was successfully uploaded.\n';
	      } else {
	        echo 'Possible file upload attack!\n';
	      }
	      echo 'Here is some more debugging info:';
	      print_r($_FILES);
	      echo "\n<hr />\n";
	      print_r($_POST);
              print "</pr" . "e>\n";
            }

            public function generate_delta(){	      
              if (file_exists('backup.mbz.delta')) {
		unlink('backup.mbz.delta');
	      }
              if (strcmp($this->method,"rdiffdir") == 0){
                exec('rm -r backup; mkdir backup; tar xf backup.mbz -C backup; rdiffdir delta backup.mbz.sig backup backup.mbz.delta');
              } else {
                exec('rdiff delta backup.mbz.sig backup.mbz backup.mbz.delta');
              }
	    }

	    public function download_patch() {
              if (file_exists($this->backup_dir.'/backup.mbz.sig')) {
	        //This is the file where we save the    information
	        $fp = fopen ($this->backup_dir.'/backup.mbz.delta','w+');
                $url = $this->url.'/backup.mbz.delta';
	        //Here is the file we are downloading, replace spaces with %20
	        $ch = curl_init(str_replace(" ","%20",$url));
	        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
	        // write curl response to file
	        curl_setopt($ch, CURLOPT_FILE, $fp); 
	        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	        // get curl response
	        curl_exec($ch); 
	        curl_close($ch);
	        fclose($fp);
              } else {
                rename($this->backup_dir.'/backup.mbz',$this->backup_dir.'/backup.mbz.backup');
                //This is the file where we save the    information
	        $fp = fopen ($this->backup_dir.'/backup.mbz','w+');
                $url = $this->url.'/backup.mbz';
	        //Here is the file we are downloading, replace spaces with %20
	        $ch = curl_init(str_replace(" ","%20",$url));
	        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
	        // write curl response to file
	        curl_setopt($ch, CURLOPT_FILE, $fp); 
	        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	        // get curl response
	        curl_exec($ch); 
	        curl_close($ch);
	        fclose($fp);
              }
	    }
	    
            public function apply_patch() {
              if (file_exists($this->backup_dir.'/backup.mbz.delta')) {
	        if (strcmp($this->method,'rdiffdir')==0) {
                  exec("sudo rdiffdir signature $this->backup_dir/backup $this->backup_dir/test.sig");
                  if(file_exists($this->backup_dir.'/test.sig')){
                    rename($this->backup_dir.'/backup.mbz',$this->backup_dir.'/backup.mbz.backup');
                    unlink($this->backup_dir.'/test.sig');
                    exec("sudo rdiffdir patch $this->backup_dir/backup $this->backup_dir/backup.mbz.delta; tar cfz $this->backup_dir/backup.tar.gz $this->backup_dir/backup;");
                    rename($this->backup_dir.'/backup.tar.gz',$this->backup_dir.'/backup.mbz');
                    echo "update complete";
                  } else {
                    echo 'rdiffdir execution not permitted, add this (www-data ALL=NOPASSWD: /usr/bin/rdiffdir
) to /etc/sudoers via visudo ... Terminated';
                  }
                } else {
                  rename($this->backup_dir.'/backup.mbz',$this->backup_dir.'/backup.mbz.backup');
	          exec("rdiff patch $this->backup_dir/backup.mbz.backup $this->backup_dir/backup.mbz.delta $this->backup_dir/backup.mbz;");
                  echo "update complete";
                }
              }
	    }         
  }
?>
