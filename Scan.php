<?php 
	/**
	* 
	*/
	class Scan 
	{		
		/**  
				i set this its doest mean this app can't read it from root but 
				its just a sample this folder is server
		*/ 
		private static $base_path = 'c:/xampp/htdocs/dropsuite/';
		private $class;
		function __construct(){
			$this->class=get_class($this);
		}
		private function _find_file($dir){
		 $tree = glob(rtrim($dir, '/') . '/*');
		 $files=null;
	    if (is_array($tree)) {
	        foreach($tree as $file) {
	            if (is_dir($file)) {
	                $files.=$this->_find_file($file);
	            } elseif (is_file($file)) {
	            	if (self::$base_path.$this->class.".php"==$file) {
	            		
	            	}else{
	            		$files.=$file.',';
	            	}
	            	
	            }
	        }
	    }
	   return $files;
		}

		private function _scan_server($path){
			
			$files       =explode(',',$this->_find_file($path));
			$clean_files =array_filter($files);
			$result      =null;
			foreach($clean_files as $f){
				$result[]=$this->_scan_file($f);
			}
			return $this->_render($result);
		}
		private  function _render($result)
		{
			$count      =array();
			$clean_data = call_user_func_array("array_merge", $result);
			$uniq_data  = array_values(array_unique($clean_data));
			$count_uniq = array_count_values($clean_data);
			
			//  you wanna biggest content right ?
			foreach($count_uniq as $cu){
				$count[]=$cu;		
			}
			$biggest=array_search($count[0], $count_uniq);
			$div=null;
			foreach($uniq_data as $ud){
				$split_biggest = str_split($biggest);
				$split_ud = str_split($ud);
				if (array_diff($split_ud, $split_biggest) !=null) {
					$div[] = 1;
				}else{
					$div[] = 0;
				}
			}
			$sum  = array_sum($div);

			return array(
				'largest_data_detail'=>$count_uniq,
				'biggest_string_contain'=>array('string'=>$biggest,'count'=>$sum+$count_uniq[$biggest]),
			);
		}
		private function _clean_dir($dir){
			return array_diff($dir, array('.','..'));
		}
		private function _scan_file($file){
				$handle   = fopen($file, "r");
				if (filesize($file)<1) {
					$contents=0;
				}else{
					$contents = fread($handle, filesize($file));
				}
				fclose($handle);
				return array($contents);
		}
		public function run (){
			return $this->_scan_server(self::$base_path);
		}
	}
	echo "<pre>";
	$scan = new scan;
	$run = $scan->run();
	print_r($run);
	echo 'Thanks it is great test! :)';
